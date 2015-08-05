<?php
/**
 * @param string $user
 * @param string $lang
 * @param \PDO $link
 * @return bool
 */
function change_admin_language($user, $lang, $link){
    
    $sql = "UPDATE synadmin SET lang = :lang WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':lang', $lang, PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);

    if ($stmt->execute() === false) {

        error_log ("Error: " . var_export($link->errorInfo(), true));
        return false;

    }

    return true;
    
}