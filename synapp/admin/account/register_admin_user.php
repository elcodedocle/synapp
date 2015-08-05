<?php
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../..'.SYNAPP_UUID_PATH.'/uuid.php';
require_once dirname(__FILE__) . '/../../account/'.SYNAPP_CONFIG_DIRNAME.'/profile_constants_constraints_defaults_and_selector_values.php';

/**
 * Adds an admin user
 *
 * @param array $reg_data
 * @param \PDO $link
 * @return array
 */
function register_admin_user($reg_data, $link)
{
    $lang = $reg_data['lang'];
    /** @noinspection PhpIncludeInspection */
    require dirname(__FILE__) . "/../../languages/{$lang}/admin/admregtxt.php";

    if (strlen($reg_data['user']) < USER_MINLENGTH) {
        return array('error'=>REG_ADM_USR_USER_TOO_SHORT);
    }
    if (strlen($reg_data['user']) > USER_MAXLENGTH) {
        return array('error'=>REG_ADM_USR_USER_TOO_LONG);
    }
    if (strlen($reg_data['password']) < PASS_MINLENGTH) {
        return array('error'=>REG_ADM_USR_PASS_TOO_SHORT);
    }
    if ($reg_data['password'] !== $reg_data['password2']) {
        return array('error'=>REG_ADM_USR_PASS_NO_MATCH);
    }
    if (($email = filter_var($reg_data['email'], FILTER_VALIDATE_EMAIL))===false) {
        return array('error'=>REG_ADM_USR_MUST_PROVIDE_VALID_EMAIL);
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

    if ($use_password_hash){
        $password = password_hash($reg_data['password'], PASSWORD_DEFAULT);
    } else {
        $password = hash('sha256', mt_rand().'$'.$reg_data['password'].NORAINBOW_SALT);
    } 
    
    $sql = "INSERT INTO `synadmin` ( `uuid`, `user`, `password`, `email`, `lang` ) VALUES ( :uuid, :user , :password , :email , :lang )";
    $stmt = $link->prepare($sql);
    $v5uuid = \synapp\info\tools\uuid\uuid::v5(\synapp\info\tools\uuid\uuid::v4(), 'synapp.info');
    $stmt->bindValue(':uuid', $v5uuid, PDO::PARAM_STR);
    $stmt->bindValue(':user', $reg_data['user'], PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':lang', $reg_data['lang'], PDO::PARAM_STR);
    if ($stmt->execute()){
        return array('uuid'=>$v5uuid);
    } else {
        error_log($link->errorInfo());
        return array('error'=>REG_ADM_COULDNT_PERFORM_DATABASE_OPERATION);
    }

}