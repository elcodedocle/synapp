<?php
use Facebook\FacebookSDKException;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\GraphUser;

define ('SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH', dirname(__FILE__) . '/..' . SYNAPP_FACEBOOK_PHP_SDK_PATH);
require_once dirname(__FILE__) . '/config/deployment_environment.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookSDKException.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookRequestException.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookAuthorizationException.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookClientException.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookServerException.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookThrottleException.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookOtherException.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/GraphObject.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/GraphSessionInfo.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/GraphUser.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/Entities/AccessToken.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookSession.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookRedirectLoginHelper.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookRequest.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/FacebookResponse.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/HttpClients/FacebookCurl.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/HttpClients/FacebookHttpable.php';
require_once SYNAPP_FACEBOOK_PHP_SDK_RELATIVE_PATH . '/HttpClients/FacebookCurlHttpClient.php';


require_once dirname(__FILE__) . '/process_registration_form.php';

require_once dirname(__FILE__) . '/../' . SYNAPP_CSPRNG_PATH . '/CryptoSecurePRNG.php';


/**
 * @param PDO $link
 * @return bool|string
 */
function process_facebook_login($link)
{
    require_once dirname(__FILE__) . '/' . SYNAPP_CONFIG_DIRNAME . '/facebook_credentials.php';
    $fbLoginRedirectUrl = SYNAPP_FB_LOGIN_REDIRECT_URL . (isset($_GET['location']) ? '?location=' . $_GET['location'] : '');
    $fbAppId = SYNAPP_FB_APP_ID;
    $fbAppSecret = SYNAPP_FB_APP_SECRET;

    $use_password_verify =
        defined('SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION')
        && (
            SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION === true
            ||
            is_string(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)
            && (trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === 'on'
                || trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === 'true'
                || trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === '1')
        ) ?
            true : false;

    try {

        if (isset($_SESSION['fb_token'])) {

            $session = new FacebookSession($_SESSION['fb_token']);

        } else {

            FacebookSession::setDefaultApplication($fbAppId, $fbAppSecret);

            $helper = new FacebookRedirectLoginHelper($fbLoginRedirectUrl);

            try {

                $session = $helper->getSessionFromRedirect();

                if (isset($session)) {

                    $_SESSION['fb_token'] = $session->getToken();

                } else {

                    return $helper->getLoginUrl(
                        array(
                            'scope' =>
                                'email'
                        )
                    );

                }

            } catch (FacebookSDKException $ex) {

                // When Facebook returns an error

                return $helper->getLoginUrl(
                    array(
                        'scope' =>
                            'email'
                    )
                );

            } catch (Exception $ex) {

                // When validation fails or other local issues

                return $helper->getLoginUrl(
                    array(
                        'scope' =>
                            'email'
                    )
                );

            }

        }

        if (isset($session)) {

            // Logged in
            $graphObject = (new FacebookRequest(
                $session, 'GET', '/me?fields=id,email,first_name,last_name'
            ))->execute()->getGraphObject(GraphUser::className());

            $sql = "SELECT * FROM `users` WHERE fbid = :fbid OR email = :email AND confirmed_email = b'1'";

            $stmt = $link->prepare($sql);
            $stmt->bindValue(':fbid', $graphObject->getProperty('id'), PDO::PARAM_STR);
            $stmt->bindValue(':email', $graphObject->getProperty('email'), PDO::PARAM_STR);
            
            if ($stmt->execute()!==false && $stmt->rowCount() > 0) {
                $user_array = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user_array['active'] == 0){
                    $_SESSION['login_err'] = 403;
                    return false;
                }
                $_SESSION['user_array'] = $user_array;
                $_SESSION['auth'] = true;
                $_SESSION['justlogged'] = true;
                $_SESSION['if_lang'] = $_SESSION['user_array']['interface_language'];
                $_SESSION['user_count'] = 1;
                $_SESSION['pass_count'] = 0;
                $_SESSION['user_array']['missed_logins'] = 0;
                $time = time();
                if (($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
                    $ip = 0;
                }
                $sql = "UPDATE users SET recovery = :recovery, missed_logins='0', last_login = :time, ip = :ip WHERE user = :user";
                $stmt = $link->prepare($sql);
                $prng = new synapp\info\tools\passwordgenerator\cryptosecureprng\CryptoSecurePRNG();
                $stmt->bindValue(':recovery', $use_password_verify ? password_hash(
                        $prng->rand(),
                        SYNAPP_PASSWORD_DEFAULT
                    ) : hash("sha256", $prng->rand()), PDO::PARAM_STR);
                $stmt->bindValue(':time', $time, PDO::PARAM_INT);
                $stmt->bindValue(':ip', $ip, PDO::PARAM_INT);
                $stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_STR);
                if ($stmt->execute() === false) {
                    error_log(var_export($link->errorInfo(), true));
                    die("Error performing database operation.");
                }
                return true;
            }

            $rd['user'] = substr(
                preg_replace(
                    "/[^a-zA-Z0-9]+/",
                    "",
                    $graphObject->getProperty('first_name') . $graphObject->getProperty('last_name')
                ),
                0,
                USER_MAXLENGTH
            );
            $i = 0;
            while (user_exist($link, $rd['user'])) {
                $rd['user'] = substr(
                    preg_replace(
                        "/[^a-zA-Z0-9]+/",
                        "",
                        $i . $graphObject->getProperty('first_name') . $graphObject->getProperty('last_name')
                    ),
                    0,
                    USER_MAXLENGTH
                );
                $i++;
            }
            $rd['pass'] = substr(hash("sha256", $_SESSION['fb_token']), 0, PASS_MAXLENGTH);
            $rd['pass2'] = $rd['pass'];
            $rd['ilang'] = $_SESSION['if_lang'];
            $rd['fbid'] = $graphObject->getProperty('id');

            $ea = process_registration_form($link, $rd, true);

            if ($ea['err'] !== true) {
                $sql = "UPDATE `users` SET fbid = :fbid, email = :email, confirmed_email = b'1' WHERE user = :user";

                $stmt = $link->prepare($sql);
                $stmt->bindValue(':fbid', $graphObject->getProperty('id'), PDO::PARAM_STR);
                $stmt->bindValue(':email', $graphObject->getProperty('email'), PDO::PARAM_STR);
                $stmt->bindValue(':user', $rd['user'], PDO::PARAM_STR);
                return ($stmt->execute() !== false);
            }

            return ($ea['err'] !== true);

        } else {

            header('Location: account/logout.php');
            die();

        }

    } catch (FacebookSDKException $ex) {

        // When Facebook returns an error

        error_log("FacebookRequestException: " . $ex->getMessage());
        header('Location: account/logout.php');
        die();

    } catch (Exception $ex) {

        // When validation fails or other local issues

        error_log("Exception on facebook login: " . $ex->getMessage());
        header('Location: account/logout.php');
        die();

    }

}