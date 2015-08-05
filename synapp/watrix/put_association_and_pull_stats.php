<?php
session_start();
require_once dirname(__FILE__) . '/../connect.php';
if (!isset($_SESSION['auth'])) {
    die("Unauthenticated Session.");
}
if ($_SESSION['auth'] !== true) {
    die("Unauthenticated Session.");
}
if (!isset($_SESSION['hashed_ticket'])) {
    die("No ticket.");
}
/**
 * @param \PDO $link
 * @param string $group
 * @param string $user
 * @param string $lang
 * @param array $pair
 * @param string $answer
 * @param int $type
 */
function associate($link, $group, $user, $lang, $pair, $answer, $type)
{
    $sql = "INSERT INTO `associations` (
          id1
        , id2
        , assigned_group
        , user
        , lang
        , word
        , time_stamp
        , type
    ) VALUES ( 
          :pairzero 
        , :pairone 
        , :group
        , :user
        , :lang
        , :answer 
        , DEFAULT 
        , :type 
    )";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':group', $group, PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    $stmt->bindValue(':lang', $lang, PDO::PARAM_STR);
    $stmt->bindValue(':pairzero', $pair[0], PDO::PARAM_STR);
    $stmt->bindValue(':pairone', $pair[1], PDO::PARAM_STR);
    $stmt->bindValue(':answer', $answer, PDO::PARAM_STR);
    $stmt->bindValue(':type', $type, PDO::PARAM_INT);
    if ($stmt->execute() === false) {
        error_log(var_export($link->errorInfo(), true));
        die("Error performing database operation.");
    }
}

/**
 * @param PDO $link
 * @param $group
 * @param $user
 * @param $lang
 * @param $pair
 * @param $answer
 * @param $type
 * @return array|bool|null
 */
function stats($link, $group, $user, $lang, $pair, $answer, $type)
{
    $sql = "SELECT * FROM `vassociations` 
        WHERE 
            assigned_group = :group 
            AND user = :user 
            AND lang = :lang 
            AND id1 = :pairzero 
            AND id2 = pairone 
            AND word = :answer 
            AND type = :type 
        GROUP BY word";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':group', $group, PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    $stmt->bindValue(':lang', $lang, PDO::PARAM_STR);
    $stmt->bindValue(':pairzero', $pair[0], PDO::PARAM_STR);
    $stmt->bindValue(':pairone', $pair[1], PDO::PARAM_STR);
    $stmt->bindValue(':answer', $answer, PDO::PARAM_STR);
    $stmt->bindValue(':type', $type, PDO::PARAM_INT);
    if ($stmt->execute()!==false) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    } else {
        return false;
    }
}

if (
    !isset($_SESSION['hashed_ticket']) ||
    !$_SESSION['hashed_ticket'] === hash(
        "sha256",
        $_POST['ticket'] . $_POST['id1'] . $_POST['id2']
    )
) {
    die ("Bad ticket.");
}
unset($_SESSION['hashed_ticket']);
$link = connect();
associate(
    $link,
    $_SESSION['user_array']['working_group'],
    $_SESSION['user_array']['user'],
    $_SESSION['user_array']['input_language'],
    array($_POST['id1'], $_POST['id2']),
    $_POST['input'],
    $_POST['type']
);

$link = null;
