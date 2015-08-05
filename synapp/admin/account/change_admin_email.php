<?php
/**
 * @param string $user
 * @param string $email
 * @param \PDO $link
 * @return bool
 */
function change_admin_email($user, $email, $link){

    if (($email = filter_var($email, FILTER_VALIDATE_EMAIL)) === false) {
        return array('error'=>ADMSET_USR_MUST_PROVIDE_VALID_EMAIL);
    }
    
    $sql = "UPDATE synadmin SET email = :email WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);

    if ($stmt->execute() === false) {

        error_log ("Error: " . var_export($link->errorInfo(), true));
        return false;

    }

    return true;

}