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
// restrict by language
$stmt = get_list($link, 'vgroup_resources_admin', array('orderby'=>array(array('name'=>'groupname')),'acceptedFilters'=>array('tresourcelang'),'filters'=>array(array('fieldname'=>'tresourcelang','placeholder'=>'tresourcelang','type'=>PDO::PARAM_STR,'operator'=>'=','value'=>$_SESSION['if_lang']),array('fieldname'=>'tresourcelang','prefix'=>'OR','operator'=>'IS NULL'))));
$data=array();
$results = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $groupnamehash = hash('sha256',$row['groupname']);
    if (!isset($results[$groupnamehash])){
        $results[$groupnamehash] = array();
        $results[$groupnamehash]['groupname'] = $row['groupname'];
        $results[$groupnamehash]['groupid'] = $row['groupid'];
        $data[][] = $row['groupname'];
        $results[$groupnamehash]['collections'] = array();
        $results[$groupnamehash]['resources_utest_pre'] = array();
        $results[$groupnamehash]['resources_utest_post'] = array();
        $results[$groupnamehash]['resources_ctest_pre'] = array();
        $results[$groupnamehash]['resources_ctest_post'] = array();
    }
    if (!in_array(array('name'=>$row['collectionname'],'val'=>$row['collectionid']),$results[$groupnamehash]['collections'])) {
        $results[$groupnamehash]['collections'][] = array('name' => $row['collectionname'], 'val' => $row['collectionid']);
    }
    if ($row['stage'] === 1) {
        if ($row['ttypeid'] === 1) {
            if (!in_array(array('name'=>$row['tresourcedesc'],'val'=>$row['resourceid']),$results[$groupnamehash]['resources_ctest_pre'])) {
                $results[$groupnamehash]['resources_ctest_pre'][] = array('name'=>$row['tresourcedesc'],'val'=>$row['resourceid']);
            }
        } else {
            if (!in_array(array('name' => $row['tresourcedesc'], 'val' => $row['resourceid']), $results[$groupnamehash]['resources_utest_pre'])) {
                $results[$groupnamehash]['resources_utest_pre'][] = array('name' => $row['tresourcedesc'], 'val' => $row['resourceid']);
            }
        }
    } else {
        if ($row['ttypeid'] === 1) {
            if (!in_array(array('name'=>$row['tresourcedesc'],'val'=>$row['resourceid']),$results[$groupnamehash]['resources_ctest_post'])) {
                $results[$groupnamehash]['resources_ctest_post'][] = array('name'=>$row['tresourcedesc'],'val'=>$row['resourceid']);
            }
        } else {
            if (!in_array(array('name' => $row['tresourcedesc'], 'val' => $row['resourceid']), $results[$groupnamehash]['resources_utest_post'])) {
                $results[$groupnamehash]['resources_utest_post'][] = array('name' => $row['tresourcedesc'], 'val' => $row['resourceid']);
            }
        }
    }
}
// query distinct groups -> they are in $result[$groupnamehash]['groupname']
// query distinct collections, make selector for each group using result collection name and id and defaults in $result[$groupnamehash]['collections']
$stmt = $link->query("SELECT * FROM image_collections");
$i=0;
$selectorValues = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $selectorValues[$i]['name']=$row['collectionname'];
    $selectorValues[$i]['val']=$row['collectionid'];
    $i++;
}
$i = 0;
foreach ($results as $result){
    $selectorDefaults = array();
    foreach ($result['collections'] as $collection){
        $selectorDefaults[]=$collection['val'];
    }
    $selectorOptions = option_list($selectorValues, $selectorDefaults);
    $selectorString = "<select class=\"groupCollectionsSelector\" name=\"groupCollections{$result['groupid']}\" id=\"groupCollections{$result['groupid']}\" data-groupid=\"{$result['groupid']}\" multiple='multiple' >";
    foreach ($selectorOptions as $option) { $selectorString .= $option; }
    $selectorString .= "</select>";
    $data[$i][1]=$selectorString;
    $i++;
}
// query distinct resources for type 1 test
$lang = $_SESSION['if_lang'];
$ttypeid = 1;
$sql = "SELECT ttr.ttypeid, ttr.tresourceid, ttr.tresourcelang, tr.tresourcedesc
FROM test_type_resources ttr
LEFT JOIN test_resources tr
ON tr.tresourceid=ttr.tresourceid and tr.tresourcelang=ttr.tresourcelang
WHERE ttr.tresourcelang=:lang AND ttypeid = :ttypeid";
$stmt = $link->prepare($sql);
$stmt->bindValue(':lang', $lang, PDO::PARAM_STR);
$stmt->bindValue(':ttypeid', $ttypeid, PDO::PARAM_INT);
$stmt->execute();
$i=0;

// make selector for each group using result collection name and id and defaults in $result[$groupnamehash]['collections']
$selectorValues = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $selectorValues[$i]['name']=$row['tresourcedesc'];
    $selectorValues[$i]['val']=$row['tresourceid'];
    $i++;
}
$i = 0;
foreach ($results as $result){
    $selectorDefaults = array();
    foreach ($result['resources_ctest_pre'] as $resource){
        $selectorDefaults[]=$resource['val'];
    }
    $selectorOptions = option_list($selectorValues, $selectorDefaults);
    $selectorString = "<select class=\"groupResourcesCTestPreSelector\" name=\"groupResourcesCTestPre{$result['groupid']}\" id=\"groupResourcesCTestPre{$result['groupid']}\" data-groupid=\"{$result['groupid']}\" data-stage=\"1\" multiple='multiple' >";
    foreach ($selectorOptions as $option) { $selectorString .= $option; }
    $selectorString .= "</select>";
    $data[$i][2]=$selectorString;
    $i++;
}
$i = 0;
foreach ($results as $result){
    $selectorDefaults = array();
    foreach ($result['resources_ctest_post'] as $resource){
        $selectorDefaults[]=$resource['val'];
    }
    $selectorOptions = option_list($selectorValues, $selectorDefaults);
    $selectorString = "<select class=\"groupResourcesCTestPostSelector\" name=\"groupResourcesCTestPost{$result['groupid']}\" id=\"groupResourcesCTestPost{$result['groupid']}\" data-groupid=\"{$result['groupid']}\" data-stage=\"2\" multiple='multiple' >";
    foreach ($selectorOptions as $option) { $selectorString .= $option; }
    $selectorString .= "</select>";
    $data[$i][3]=$selectorString;
    $i++;
}

// query distinct resources for type 2 test
$ttypeid=2;
$stmt = $link->prepare($sql);
$stmt->bindValue(':lang', $lang, PDO::PARAM_STR);
$stmt->bindValue(':ttypeid', $ttypeid, PDO::PARAM_INT);
$stmt->execute();
$i=0;

// make selector for each group using result collection name and id and defaults in $result[$groupnamehash]['collections']
$selectorValues = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $selectorValues[$i]['name']=$row['tresourcedesc'];
    $selectorValues[$i]['val']=$row['tresourceid'];
    $i++;
}
$i = 0;
foreach ($results as $result){
    $selectorDefaults = array();
    foreach ($result['resources_utest_pre'] as $resource){
        $selectorDefaults[]=$resource['val'];
    }
    $selectorOptions = option_list($selectorValues, $selectorDefaults);
    $selectorString = "<select class=\"groupResourcesUTestPreSelector\" name=\"groupResourcesUTestPre{$result['groupid']}\" id=\"groupResourcesUTestPre{$result['groupid']}\" data-groupid=\"{$result['groupid']}\" data-stage=\"1\" multiple='multiple' >";
    foreach ($selectorOptions as $option) { $selectorString .= $option; }
    $selectorString .= "</select>";
    $data[$i][4]=$selectorString;
    $i++;
}
$i = 0;
foreach ($results as $result){
    $selectorDefaults = array();
    foreach ($result['resources_utest_post'] as $resource){
        $selectorDefaults[]=$resource['val'];
    }
    $selectorOptions = option_list($selectorValues, $selectorDefaults);
    $selectorString = "<select class=\"groupResourcesUTestPostSelector\" name=\"groupResourcesUTestPost{$result['groupid']}\" id=\"groupResourcesUTestPost{$result['groupid']}\" data-groupid=\"{$result['groupid']}\" data-stage=\"2\" multiple='multiple' >";
    foreach ($selectorOptions as $option) { $selectorString .= $option; }
    $selectorString .= "</select>";
    $data[$i][5]=$selectorString;
    $i++;
}


$dataSet = (object) array(
    'tableDataSet' => (object) array (
        'data' => $data,
        'columns' => get_user_groups_list_fieldnames(),
    ),
);
$link = null;
header('Content-Type: application/json');
echo json_encode($dataSet);
