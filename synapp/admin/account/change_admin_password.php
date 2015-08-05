<?php
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../../account/'.SYNAPP_CONFIG_DIRNAME.'/profile_constants_constraints_defaults_and_selector_values.php';
/**
 * @param $user
 * @param string $old
 * @param string $pass
 * @param \PDO $link
 * @return bool
 */
function change_admin_password($user, $old, $pass, $link)
{

    $sql = "SELECT password FROM synadmin WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user);

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

    if ($stmt->execute()!== false && $ua = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($ua['password'] !== 'nologin'){
            
            if ($use_password_verify){
                if (!password_verify($old, $ua['password'])){
                    return false;
                }
            } else {
                $saltNhash = explode('$', $ua['password']);
                if ($saltNhash[1]!==hash("sha256",$saltNhash[0].$old.NORAINBOW_SALT)){
                    return false;
                }
            }
            
        }

    } else {

        $link = null;
        return false;

    }

    return change_admin_password_no_verify($user, $pass, $link);

}

/**
 * @param string $user
 * @param string $pass
 * @param \PDO $link
 * @return bool
 */
function change_admin_password_no_verify($user, $pass, $link){


    if (strlen($pass) < PASS_MINLENGTH) {
        return array('error'=>'PASS_TOO_SHORT');
    }
    
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
        $salt = mt_rand();
        $hashedPassword = $salt . '$' . hash("sha256", $pass . $salt . NORAINBOW_SALT);
    }
    $sql = "UPDATE synadmin SET password = :hashedpass WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':hashedpass', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);

    if ($stmt->execute() === false) {

        error_log ("Error: " . var_export($link->errorInfo(), true));
        return false;

    }

    return true;
    
}