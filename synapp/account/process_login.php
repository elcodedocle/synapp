<?php
require_once dirname(__FILE__) . '/config/deployment_environment.php';
require_once dirname(
        __FILE__
    ) . '/' . SYNAPP_CONFIG_DIRNAME . '/profile_constants_constraints_defaults_and_selector_values.php';
require_once dirname(__FILE__) . '/../' . SYNAPP_CSPRNG_PATH . '/CryptoSecurePRNG.php';

/**
 * @param string $user
 * @param string $pass
 * @param PDO $link
 * @param bool $confirm
 * @return bool
 */
function process_login($user, $pass, $link, $confirm = false)
{
    $_SESSION['auth'] = false;
    $_SESSION['user'] = $user;
    if (($_SESSION['user_count'] > 6) && (!captcha_verify_word())) {
        $_SESSION['login_err'] = 3;
        return false;
    }
    $sql = "SELECT * FROM users WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    if ($stmt->execute()!==false && $user_array = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['pass_count'] = $user_array['missed_logins'];
        if ($user_array['active'] == 0){
            $_SESSION['login_err'] = 403;
        }
        if ((($user_array['missed_logins'] > 9) || ($confirm)) && (!captcha_verify_word())) {
            $_SESSION['login_err'] = 3;
            return false;
        }

        $hashOK = false;
        $use_password_verify = defined('SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION') ?
            (
            is_string(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION) ?
                strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)
                :
                SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION
            )
            :
            false;
        if ($use_password_verify === true || $use_password_verify === 'on' || $use_password_verify === 'true') {
            $use_password_verify = true;
            if (password_verify($pass, $user_array['pass'])) {
                $hashOK = true;
            }
        } else {
            if ($user_array['pass'] === hash("sha256", $pass . NORAINBOW_SALT)) {
                $hashOK = true;
            }
        }
        $prng = new synapp\info\tools\passwordgenerator\cryptosecureprng\CryptoSecurePRNG();
        if ($hashOK) {
            $_SESSION['user_array'] = $user_array;
            $_SESSION['auth'] = true;
            $_SESSION['justlogged'] = true;
            $_SESSION['if_lang'] = $_SESSION['user_array']['interface_language'];
            $_SESSION['user_count'] = 1;
            $_SESSION['pass_count'] = 0;
            $user_array['missed_logins'] = 0;
            $time = time();
            if (!$confirm) {
                $sql = "UPDATE users SET recovery = :recovery WHERE user = :user";
                $stmt = $link->prepare($sql);
                $stmt->bindValue(':recovery', $use_password_verify ? password_hash(
                        $prng->rand(),
                        SYNAPP_PASSWORD_DEFAULT
                    ) : hash("sha256", $prng->rand()), PDO::PARAM_STR);
                $stmt->bindValue(':user',$user, PDO::PARAM_STR);

                if ($stmt->execute() === false) {
                    error_log(var_export($link->errorInfo(), true));
                    die("Error performing database operation.");
                }
                
            }
            if (($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
                $ip = 0;
            }
            $sql = "UPDATE users SET missed_logins='0', last_login = :time, ip = :ip WHERE user = :user";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':time', $time, PDO::PARAM_INT);
            $stmt->bindValue(':ip', $ip, PDO::PARAM_INT);
            $stmt->bindValue(':user', $user, PDO::PARAM_STR);
            if ($stmt->execute() === false) {
                error_log(var_export($link->errorInfo(), true));
                die("Error performing database operation.");
            }
            return true;
        } else {
            if (
                $user_array['last_login'] < PRENORAINBOW_TIMESTAMP && $user_array['pass'] === hash(
                    "sha256",
                    hash("sha256", $pass) . NORAINBOW_SALT
                )
                ||
                $use_password_verify && $user_array['last_login'] < PRENOPHASH_TIMESTAMP && $user_array['pass'] === hash(
                    "sha256",
                    $pass . NORAINBOW_SALT
                )
            ) {
                $_SESSION['user_array'] = $user_array;
                $_SESSION['auth'] = true;
                $_SESSION['justlogged'] = true;
                $_SESSION['if_lang'] = $_SESSION['user_array']['interface_language'];
                $_SESSION['user_count'] = 1;
                $_SESSION['pass_count'] = 0;
                $user_array['missed_logins'] = 0;
                $time = time();
                if (!$confirm) {
                    $sql = "UPDATE users SET recovery = :recovery WHERE user = :user";
                    $stmt = $link->prepare($sql);
                    $stmt->bindValue(':recovery', $use_password_verify ? password_hash(
                            $prng->rand(),
                            SYNAPP_PASSWORD_DEFAULT
                        ) : hash("sha256", $prng->rand()), PDO::PARAM_STR);
                    $stmt->bindValue(':user',$user, PDO::PARAM_STR);

                    if ($stmt->execute() === false) {
                        error_log(var_export($link->errorInfo(), true));
                        die("Error performing database operation.");
                    }
                    
                }
                if (($ip = ip2long($_SERVER['REMOTE_ADDR'])) === false) {
                    $ip = 0;
                }
                $passParamValue = $use_password_verify ? password_hash(
                    $pass,
                    SYNAPP_PASSWORD_DEFAULT
                ) : hash("sha256", $pass . NORAINBOW_SALT);
                $sql = "UPDATE users SET missed_logins='0', last_login=:time, ip=:ip, pass = :pass WHERE user = :user";
                $stmt = $link->prepare($sql);
                $stmt->bindValue(':time', $time, PDO::PARAM_INT);
                $stmt->bindValue(':ip', $ip, PDO::PARAM_INT);
                $stmt->bindValue(':pass', $passParamValue, PDO::PARAM_STR);
                $stmt->bindValue(':user', $user, PDO::PARAM_STR);
                if ($stmt->execute() === false) {
                    error_log(var_export($link->errorInfo(), true));
                    die("Error performing database operation.");
                }
                
                return true;
                
            } else {
                $_SESSION['login_err'] = 1;
                $user_array['missed_logins']++;
                $_SESSION['pass_count'] = $user_array['missed_logins'];
                $sql = "UPDATE users SET missed_logins = :missed_logins WHERE user = :user";
                $stmt = $link->prepare($sql);
                $stmt->bindValue(':missed_logins', $user_array['missed_logins'], PDO::PARAM_INT);
                $stmt->bindValue(':user',$user, PDO::PARAM_STR);

                if ($stmt->execute() === false) {
                    error_log(var_export($link->errorInfo(), true));
                    die("Error performing database operation.");
                }
                
                return false;
            }
        }
    } else {
        $_SESSION['login_err'] = 2;
        $_SESSION['user_count']++;
        return false;
    }
}

/**
 * @param string $user
 * @param string $pass
 * @param PDO $link
 * @return bool
 */
function process_android_login($user, $pass, $link)
{
    $_SESSION['auth'] = false;
    $_SESSION['user'] = $user;
    $sql = "SELECT * FROM users WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    if ($stmt->execute()!==false && $user_array = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($user_array['pass'] === hash("sha256", $pass)) {
            $_SESSION['user_array'] = $user_array;
            $_SESSION['auth'] = true;
            $_SESSION['justlogged'] = true;
            $_SESSION['if_lang'] = $_SESSION['user_array']['interface_language'];
            $_SESSION['user_count'] = 1;
            $_SESSION['pass_count'] = 0;
            $user_array['missed_logins'] = 0;
            return true;
        }
    } else {
        return false;
    }
    return false;
}
