<?php
session_start();
require_once dirname(__FILE__) . '/../../utils/http_response_code.php';
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    http_response_code(401);
    die();
}
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../../connect.php';
require_once dirname(__FILE__) . '/get_list_fieldnames.php';
require_once dirname(__FILE__) . '/../common/get_list.php';
$link = connect();
$stmt = get_list($link, 'vimages');
$data=array();
$collectionIds = array();
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $collectionIds[] = $row[2];
    array_splice($row, 2, 1);
    $data[] = $row;
}
$dataSet = (object) array(
    'tableDataSet' => (object) array (
        'data' => $data,
        'columns' => get_resources_list_fieldnames()
    ),
    'collectionIds' => $collectionIds,
);
header('Content-Type: application/json');
echo json_encode($dataSet);
