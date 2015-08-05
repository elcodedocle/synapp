<?php
session_start();
require_once dirname(__FILE__).'/../../account/config/deployment_environment.php';
require_once dirname(__FILE__).'/../../connect.php';
require_once dirname(__FILE__) . '/../../utils/http_response_code.php';
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    http_response_code(401);
    die("Bad auth");
}
$link = connect();
$sql = "UPDATE `users` SET active = :active WHERE `user` = :user";
$stmt = $link->prepare($sql);
$stmt->bindValue(':active',$_REQUEST['active'],PDO::PARAM_INT);
$stmt->bindValue(':user',$_REQUEST['user'],PDO::PARAM_STR);
if (!$stmt->execute()){
    http_response_code(500);
    error_log("error setting active param with value ".(isset($_REQUEST['active'])?$_REQUEST['active']:'(no value)')." for user ".(isset($_REQUEST['user'])?$_REQUEST['user']:'(no value)').PHP_EOL.var_export($link->errorInfo(),true));
    header('Content-Type: application/json');
    die (json_encode((object)array('result'=>'KO')));
}
header('Content-Type: application/json');
echo json_encode((object)array('result'=>'OK'));
