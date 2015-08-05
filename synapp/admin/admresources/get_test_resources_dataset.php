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
$stmt = get_list($link, 'vtest_type_resources');
$data=array();
$testResourceIds = array();
$langs = array($_SESSION['if_lang']);
foreach ($_SESSION['interface_languages'] as $language){
    if ($language['val']!==$_SESSION['if_lang']){
        $langs[] = $language['val'];
    }
}
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!isset($data[$row['tresourceid']])) {
        $data[$row['tresourceid']] = array(array());
    }
    $data[$row['tresourceid']][0][$row['ttypeid']] = array('name'=>$row['ttypedesc'],'val'=>$row['ttypeid']);
    $data[$row['tresourceid']][array_search($row['tresourcelang'],$langs)+1]=$row['tresourcedesc'];
    $testResourceIds[$row['tresourceid']]=$row['tresourceid'];
}
$data = array_values($data);
$testResourceIds = array_values($testResourceIds);
$stmt = $link->query("SELECT * FROM test_types");
$i=0;
$selectorValues = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $selectorValues[$i]['name']=$row['ttypedesc'];
    $selectorValues[$i]['val']=$row['ttypeid'];
    $i++;
}
foreach ($data as $i=>$register){
    $selectorDefaults = array();
    foreach ($register[0] as $ttype){
        $selectorDefaults[]=$ttype['val'];
    }
    $selectorOptions = option_list($selectorValues, $selectorDefaults);
    $selectorString = "<select class=\"testResourcesTypesSelector\" name=\"testResourceTypes{$testResourceIds[$i]}\" id=\"testResourceTypes{$testResourceIds[$i]}\" data-resourceid=\"{$testResourceIds[$i]}\" multiple='multiple'\">";
    foreach ($selectorOptions as $option) { $selectorString .= $option; }
    $selectorString .= "</select>";
    $data[$i][0]=$selectorString;
    $i++;
}
$dataSet = (object) array(
    'tableDataSet' => (object) array (
        'data' => $data,
        'columns' => get_test_resources_list_fieldnames()
    ),
    'testResourceIds' => $testResourceIds,
    'testResourceLangs' => $langs,
);
header('Content-Type: application/json');
echo json_encode($dataSet);
