<?php
require_once dirname(__FILE__) . '/config/deployment_environment.php';
require_once dirname(
        __FILE__
    ) . '/' . SYNAPP_CONFIG_DIRNAME . '/profile_constants_constraints_defaults_and_selector_values.php';

/**
 * @param string $code
 * @param string $user
 * @param string $pass
 */
function change_password($code, $user, $pass)
{
    $link = connect();
    $sql = "SELECT recovery FROM users WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user',$user,PDO::PARAM_STR);
    $stmt->execute();
    if ($ua = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!(hash("sha256", $code) === $ua['recovery'])) {
            $link = null;
            die ("Error: Invalid request code (2).");
        }
    } else {
        $link = null;
        die ("Error: User not found (2).");
    }
    $recovery = hash("sha256", mt_rand());
    $use_password_hash =
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
    if ($use_password_hash) {
        $hashedPassword = password_hash($pass, SYNAPP_PASSWORD_DEFAULT);
    } else {
        $hashedPassword = hash("sha256", $pass . NORAINBOW_SALT);
    }
    $sql = "UPDATE users SET pass = :hashedPassword, recovery = :recovery WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindValue(':recovery', $recovery, PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    ;
    if ($stmt->execute() === false) {
        die ("Error: " . var_export($link->errorInfo(), true));
    }
    $link = null;
    echo "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><title>" . PR_TITLE_3 . " " . $user . "</title></head><body>
    <p>" . PR_DONE_1 . " " . $user . " " . PR_DONE_2 . "</p>
    <p><a href='../index.php'>" . PR_HOME . "</a></p>
    </body></html>";

}
