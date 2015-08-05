<?php

/**
 * @param PDO $link
 * @param string $user
 * @return bool
 */
function user_exist($link, $user)
{
    $sql = "SELECT * FROM users WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user);
    
    if ($stmt->execute()!==false) {
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return true;
        }
    }
    return false;
}

/**
 * @param PDO $link
 * @param string $email
 * @return bool
 */
function email_exist($link, $email)
{
    if (strlen($email) > 4) {
        $sql = "select * from users WHERE email = :email";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':email', $email);
        if ($stmt->execute()!==false) {
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return true;
            }
        }
    }
    return false;
}
