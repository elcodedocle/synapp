<?php
session_start();
require_once dirname(__FILE__).'/../../account/config/deployment_environment.php';
require_once dirname(__FILE__).'/../../connect.php';
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    http_response_code(401);
    die("Bad auth");
}
$link = connect();
$stmt = $link->prepare("SELECT `collectionid` FROM `images` WHERE `id` = :id");
$stmt->bindValue(':id',$_REQUEST['id'],PDO::PARAM_STR);
if (!$stmt->execute()){
    http_response_code(500);
    error_log("error deleting resource with id ".(isset($_REQUEST['id'])?$_REQUEST['id']:'(no value)').PHP_EOL.var_export($link->errorInfo(),true));
    header('Content-Type: application/json');
    die (json_encode((object)array('result'=>'KO')));
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$collectionid = $row['collectionid'];
$sql = "DELETE FROM `images` WHERE `id` = :id";
$stmt = $link->prepare($sql);
$stmt->bindValue(':id',$_REQUEST['id'],PDO::PARAM_STR);
if (!$stmt->execute()){
    http_response_code(500);
    error_log("error deleting resource with id ".(isset($_REQUEST['id'])?$_REQUEST['id']:'(no value)').PHP_EOL.var_export($link->errorInfo(),true));
    header('Content-Type: application/json');
    die (json_encode((object)array('result'=>'KO')));
}


$stmt = $link->prepare("SELECT * FROM `group_collections` WHERE `collectionid` = :collectionid");
$stmt->bindValue(':collectionid',$collectionid, PDO::PARAM_INT);
if ($stmt->execute() !== false && $stmt->rowCount() === 0){
    $stmt = $link->prepare("SELECT * FROM `images` WHERE `collectionid` = :collectionid");
    $stmt->bindValue(':collectionid',$collectionid, PDO::PARAM_INT);
    if ($stmt->execute() !== false && $stmt->rowCount() === 0){
        $stmt = $link->prepare("DELETE FROM `image_collections` WHERE `collectionid` = :collectionid");
        $stmt->bindValue(':collectionid',$collectionid, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header('Content-Type: application/json');
echo json_encode((object)array('result'=>'OK'));
