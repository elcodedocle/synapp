<?php
require_once dirname(__FILE__) . '/../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../' . SYNAPP_CSPRNG_PATH . '/CryptoSecurePRNG.php';
/**
 * @param string $email
 * @param \PDO $link
 */
function send_confirmation_email($email, $link)
{
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
    $user = $_SESSION['user_array']['user'];
    $prng = new synapp\info\tools\passwordgenerator\cryptosecureprng\CryptoSecurePRNG(); 
    $code = $prng->rand(); //validation code
    
    $sql = "UPDATE users SET email_confirmation_code = :confirmationcode, confirmed_email = b'0' WHERE user = :user";
    
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':confirmationcode',$use_password_verify ? password_hash(
            $code,
            SYNAPP_PASSWORD_DEFAULT
        ) : hash("sha256", $code), PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    $stmt->execute();
    
    $subject = PR_MAIL_SUBJECT_1 . $user . PR_MAIL_SUBJECT_2;
    $mime_boundary = hash("sha256", time());
    $msg = PR_MAIL_TO . " " . $email;
    $msg .= " " . PR_MAIL_USR_1 . " " . $user;
    $msg .= PR_MAIL_USR_2 . PHP_EOL . PHP_EOL;
    $msg .= PR_MAIL_NR . PHP_EOL . PHP_EOL;
    $msg .= PR_MAIL_IGNORE . PHP_EOL . PHP_EOL;
    $msg .= PR_MAIL_DO . PHP_EOL . PHP_EOL;
    $msg .= SYNAPP_BASE_URL_HTTP . "/account/confirm_email.php?user=" . $user . "&code=" . $code . " " . PR_MAIL_DO_HTTP . PHP_EOL . PHP_EOL;
    $msg .= SYNAPP_BASE_URL_HTTPS . "/account/confirm_email.php?user=" . $user . "&code=" . $code . " " . PR_MAIL_DO_HTTPS;
    $msg .= PHP_EOL . PHP_EOL;
    $msg .= "--" . $mime_boundary . "--" . PHP_EOL . PHP_EOL; // finish with two eol's for better security. see Injection. 
    # Common Headers
    $time = time();
    $now = (int)(date('Y', $time) . date('m', $time) . date('j', $time));
    $headers = 'From: SYNAPP mailer <'.SYNAPP_NO_REPLY.'@'.SYNAPP_MAIL_DOMAIN.'>' . PHP_EOL;
    $headers .= 'Reply-To: noreply <'.SYNAPP_NO_REPLY.'@'.SYNAPP_MAIL_DOMAIN.'>' . PHP_EOL;
    $headers .= 'Return-Path: noreply <'.SYNAPP_NO_REPLY.'@'.SYNAPP_MAIL_DOMAIN.'>' . PHP_EOL; // these two to set reply address
    $headers .= "Message-ID:<" . $now . " admin@" . $_SERVER['SERVER_NAME'] . ">" . PHP_EOL;
    $headers .= "X-Mailer: PHP v" . phpversion() . PHP_EOL; // These two to help avoid spam-filters
    # Boundry for marking the split & Multitype Headers
    $headers .= 'MIME-Version: 1.0' . PHP_EOL;
    $headers .= "Content-Type: text/plain; charset=\"utf-8\"" . PHP_EOL . PHP_EOL;
    mail($email, $subject, $msg, $headers);
    $sql = "UPDATE users SET confirmed_email = 0 WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user);
    $stmt->execute();
    $_SESSION['user_array']['confirmed_email'] = chr(0);
}
