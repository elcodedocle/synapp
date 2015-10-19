<?php
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../..'.SYNAPP_UUID_PATH.'/uuid.php';
require_once dirname(__FILE__) . '/../../account/'.SYNAPP_CONFIG_DIRNAME.'/profile_constants_constraints_defaults_and_selector_values.php';

/**
 * Logs an admin user
 *
 * @param string $user
 * @param string $pass
 * @param PDO $link
 * @return bool|string
 */
function process_adm_login($user, $pass, $link)
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

    $sql = "SELECT * FROM synadmin WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->execute() === false) {
        return array('err'=>true, 'errCod'=>'OP_ERR','errMsg'=>ADMLOGIN_ERR_501);
    } else {
        if ($stmt->rowCount() <= 0) {
            return array('err'=>true, 'errCod'=>'USER_ERR','errMsg'=>ADMLOGIN_USERNAME_ERR);
        }
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $passErr = array('err'=>true, 'errCod'=>'PASS_ERR','errMsg'=>ADMLOGIN_PASSWORD_ERR);
    if ($use_password_verify){
        return (password_verify($pass, $row['password']))?start_admin_session($row, $link):$passErr;
    } else {
        $saltNhash = explode('$',$row['password']);
        if ($saltNhash === false || count($saltNhash)!==2){
            return false;
        }
        return $saltNhash[1]===hash("sha256",$saltNhash[0].$pass.NORAINBOW_SALT)?start_admin_session($row, $link):$passErr;
    }

}

/**
 * @param array $row
 * @param \PDO $link
 * @return string|array
 */
function start_admin_session($row, $link){

    $sql = "INSERT INTO `synadmin_session` ( `sessionid`, `uuid` ) VALUES ( :sessionid, :uuid )";
    $stmt = $link->prepare($sql);
    $v5sessionUuid = \synapp\info\tools\uuid\uuid::v5(\synapp\info\tools\uuid\uuid::v4(), SYNAPP_MAIL_DOMAIN);
    $stmt->bindValue(':sessionid', $v5sessionUuid, PDO::PARAM_STR);
    $stmt->bindValue(':uuid', $row['uuid'], PDO::PARAM_STR);

    $sql = "INSERT INTO `synadmin_session_log` ( `sessionid`, `ip` ) VALUES ( :sessionid, :ip )";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':sessionid', $v5sessionUuid, PDO::PARAM_STR);
    $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
   
    if ($stmt->execute()){
        $row['session'] = array(
            'sessionid'=>$v5sessionUuid,
            'ip' => $_SERVER['REMOTE_ADDR'],
        );
        return $row;
    } else {
        error_log($link->errorInfo());
        return array('err'=>true, 'errCod'=>'OP_ERR','errMsg'=>ADMLOGIN_COULDNT_PERFORM_DATABASE_OPERATION);
    }
    
}