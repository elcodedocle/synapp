<?php
session_start();
require_once dirname(__FILE__).'/../../account/config/deployment_environment.php';
require_once dirname(__FILE__).'/../../connect.php';
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    http_response_code(401);
    die("Bad auth");
}
$link = connect();
$sql = "DELETE FROM `test_resources` WHERE `tresourceid` = :id";
$stmt = $link->prepare($sql);
$stmt->bindValue(':id',$_REQUEST['id'],PDO::PARAM_STR);
if (!$stmt->execute()){
    http_response_code(500);
    error_log("error deleting resource with id ".(isset($_REQUEST['id'])?$_REQUEST['id']:'(no value)').PHP_EOL.var_export($link->errorInfo(),true));
    header('Content-Type: application/json');
    die (json_encode((object)array('result'=>'KO')));
}
header('Content-Type: application/json');
echo json_encode((object)array('result'=>'OK'));
