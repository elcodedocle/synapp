<?php
session_start();
require_once dirname(__FILE__) . '/../../utils/http_response_code.php';
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    http_response_code(401);
    die();
}
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../../connect.php';
require_once dirname(__FILE__) . '/get_admin_users_list_fieldnames.php';
require_once dirname(__FILE__) . '/../common/get_list.php';
$link = connect();
$stmt = get_list($link);
$data = $stmt->fetchAll(PDO::FETCH_NUM);
$dataSet = (object) array(
    'data' => $data,
    'columns' => get_admin_users_list_fieldnames()
);
header('Content-Type: application/json');
echo json_encode($dataSet);
