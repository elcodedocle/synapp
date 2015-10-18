<?php

require_once dirname(__FILE__) . '/../' . SYNAPP_CSPRNG_PATH . '/CryptoSecurePRNG.php';

/**
 * @param string $input
 */
function password_restore_email($input)
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
    
    $link = connect();
    if (parse_email($input, 0) == 0) {
        $sql = "SELECT * FROM users WHERE email = :input AND confirmed_email = 1";
        $isemail = true;
    } else {
        $sql = "SELECT * FROM users WHERE user = :input";
        $isemail = false;
    }
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':input', $input, PDO::PARAM_STR);
    $stmt->execute();
    $link = null;
    $ua = $stmt->fetch(PDO::FETCH_ASSOC);
    $emailnotfound = true;
    $usernotfound = true;
    if (isset($ua['email']) && $ua['email'] !== '') {
        $usernotfound = false;
        if ($ua['email'] != '' && parse_email($ua['email']) == 0 && $ua['confirmed_email'] == 1) {
            $emailnotfound = false;
        }
    }
    if (!$emailnotfound) {
        $user = $ua['user'];
        $link = connect();
        $sql = "UPDATE users SET recovery = :recovery WHERE user = :user";
        $stmt = $link->prepare($sql);
        $prng = new synapp\info\tools\passwordgenerator\cryptosecureprng\CryptoSecurePRNG();
        $code = $prng->rand();
        $stmt->bindValue(':recovery', $use_password_verify ? password_hash(
                $code,
                SYNAPP_PASSWORD_DEFAULT
            ) : hash("sha256", $code), PDO::PARAM_STR);
        $stmt->bindValue(':user', $user);
        $stmt->execute();
        $email = $ua['email'];
        $to = $email;
        $subject = passresout('subject', $user);
        $mime_boundary = hash("sha256", time());
        $msg = passresout('email', $user, $email, $code, $mime_boundary);
        $headers = passresout('headers', $mime_boundary);
        mail($to, $subject, $msg, $headers);
        if ($isemail) {
            echo passresout('prmailsenttoaddress', $input);
        } else {
            echo passresout('prmailsenttouser', $input);
        }
    } else {
        if ($isemail) {
            echo passresout('emailnotfound', $input);
        } else {
            if ($usernotfound) {
                echo passresout('usernotfound', $input);
            } else {
                echo passresout('novalidemailassociated', $input);
            }
        }
    }
}
