<?php
session_start();
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    header("Location: ../admlogin.phtml?location=admresources.phtml");
    die();
}
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../../connect.php';
require_once dirname(__FILE__) . '/../../utils/http_response_code.php';

$link = connect();
$output = array('result'=>'KO', 'error'=>'Invalid request.');

// if a group name is defined, assume creation (we do not allow edition of group names)
if (isset($_POST['group_name'])) {
    $sql = "INSERT INTO `groups` (`name`) VALUES (:groupname)";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':groupname', $_POST['group_name'], PDO::PARAM_STR);
    if ($stmt->execute() === false) {

        error_log("Error: " . var_export($link->errorInfo(), true));
        http_response_code(500);
        $output = array('result'=>'KO', 'error'=>'Error storing group data.');

    } else {
        $output = array('result'=>'OK');
        $groupid = $link->lastInsertId();
    }
} else {
    $groupid = $_POST['groupid'];
}
if (isset($_POST['resourceid'])  && isset($_POST['stage'])  && isset($groupid)){
    if (isset($_POST['deleteEntry']) && $_POST['deleteEntry']==='true'){
        $sql = "DELETE FROM `group_resources` WHERE `groupid` = :groupid AND `resourceid` = :resourceid AND `stage` = :stage";
    } else {
        $sql = "INSERT INTO `group_resources` (`groupid`, `resourceid`, `stage`) VALUES (:groupid, :resourceid, :stage)";
    }
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':groupid',$groupid, PDO::PARAM_INT);
    $stmt->bindValue(':resourceid',$_POST['resourceid'], PDO::PARAM_INT);
    $stmt->bindValue(':stage',$_POST['stage'], PDO::PARAM_INT);
    if ($stmt->execute() === false) {

        error_log("Error: " . var_export($link->errorInfo(), true));
        http_response_code(500);
        $output = array('result'=>'KO', 'error'=>'Error storing group data.');

    } else {
        $output = array('result'=>'OK');
    }
}
if (isset($_POST['collectionid']) && isset($groupid)){
    if (isset($_POST['deleteEntry']) && $_POST['deleteEntry']==='true'){
        $sql = "DELETE FROM `group_collections` WHERE `groupid` = :groupid AND `collectionid` = :collectionid";
    } else {
        $sql = "INSERT INTO `group_collections` (`groupid`, `collectionid`) VALUES (:groupid, :collectionid)";
    }
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':groupid',$groupid, PDO::PARAM_INT);
    $stmt->bindValue(':collectionid',$_POST['collectionid'], PDO::PARAM_INT);
    if ($stmt->execute() === false) {

        error_log("Error: " . var_export($link->errorInfo(), true));
        http_response_code(500);
        $output = array('result'=>'KO', 'error'=>'Error storing group data.');

    } else {

        if (isset($_POST['deleteEntry']) && $_POST['deleteEntry']==='true'){
            $stmt = $link->prepare("SELECT * FROM `group_collections` WHERE `collectionid` = :collectionid");
            $stmt->bindValue(':collectionid',$_POST['collectionid'], PDO::PARAM_INT);
            if ($stmt->execute() !== false && $stmt->rowCount() === 0){
                $stmt = $link->prepare("SELECT * FROM `images` WHERE `collectionid` = :collectionid");
                $stmt->bindValue(':collectionid',$_POST['collectionid'], PDO::PARAM_INT);
                if ($stmt->execute() !== false && $stmt->rowCount() === 0){
                    $stmt = $link->prepare("DELETE FROM `image_collections` WHERE `collectionid` = :collectionid");
                    $stmt->bindValue(':collectionid',$_POST['collectionid'], PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
        
        $output = array('result'=>'OK');
    }
}

$link = null;

header('Content-Type: application/json');
echo json_encode($output);
