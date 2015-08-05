<?php
session_start();
require_once dirname(__FILE__).'/../../account/config/deployment_environment.php';
require_once dirname(__FILE__).'/../../connect.php';
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    http_response_code(401);
    die("Bad auth");
}
$link = connect();
$sql = "UPDATE `images` SET name = :name WHERE `id` = :id";
$stmt = $link->prepare($sql);
$stmt->bindValue(':id',$_POST['id'],PDO::PARAM_STR);
$stmt->bindValue(':name',$_POST['name'],PDO::PARAM_STR);
if (!$stmt->execute()){
    http_response_code(500);
    error_log("error setting name for resource with id ".(isset($_POST['id'])?$_POST['id']:'(no value)').PHP_EOL.var_export($link->errorInfo(),true));
    header('Content-Type: application/json');
    die (json_encode((object)array('result'=>'KO')));
}
header('Content-Type: application/json');
echo json_encode((object)array('result'=>'OK'));
