<?php
session_start();
require_once dirname(__FILE__).'/../../account/config/deployment_environment.php';
require_once dirname(__FILE__).'/../../connect.php';
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    http_response_code(401);
    die("Bad auth");
}
$link = connect();
$result = (object)array('result'=>'OK');
if (isset($_POST['categoryid']) && !isset($_POST['contentid'])){
    if (isset($_POST['delete'])&&$_POST['delete']==='true'){
        $sql = "DELETE FROM `dynamic_content_categories` WHERE `categoryid` = :categoryid";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':categoryid',$_POST['categoryid'],PDO::PARAM_INT);
    } else if (isset($_POST['add']) && $_POST['add']==='true'){
        $sql = "INSERT INTO `dynamic_content_categories` (`weight`, `parentcategoryid`) VALUES (-1, 2)";
        $stmt = $link->prepare($sql);
        if (!$stmt->execute()) {
            $result->result = 'KO';
        } else {
            $result->id = $link->lastInsertId();
            $sql = "INSERT INTO `dynamic_content_categories_data` (`categoryid`,`categoryname`,`lang`) VALUES (:categoryid,'', :lang)";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':categoryid', $result->id, PDO::PARAM_STR);
            $stmt->bindValue(':lang', $_POST['lang'], PDO::PARAM_STR);
            if (!$stmt->execute()) {
                $result->result = 'KO';
            } else {
                $addCategorySuccess = true;
            }
        }
    } else if (isset($_POST['categoryname'])){
        $sql = "UPDATE `dynamic_content_categories_data` SET `categoryname` = :categoryname WHERE `categoryid` = :categoryid AND `lang` = :lang";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':categoryid',$_POST['categoryid'],PDO::PARAM_INT);
        $stmt->bindValue(':categoryname',$_POST['categoryname'],PDO::PARAM_STR);
        $stmt->bindValue(':lang',$_POST['lang'],PDO::PARAM_STR);
    } else if (isset($_POST['weight'])){
        $sql = "UPDATE `dynamic_content_categories` SET `weight` = :weight WHERE `categoryid` = :categoryid";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':categoryid',$_POST['categoryid'],PDO::PARAM_INT);
        $stmt->bindValue(':weight',$_POST['weight'],PDO::PARAM_INT);
    }
}
if (isset($_POST['contentid'])||isset($addCategorySuccess)&&$addCategorySuccess===true){
    if (isset($_POST['delete'])&&$_POST['delete']==='true'){
        $sql = "DELETE FROM `dynamic_content` WHERE `contentid` = :contentid";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':contentid',$_POST['contentid'],PDO::PARAM_INT);
    } else if (isset($_POST['add'])&&$_POST['add']==='true'){
        $sql = "INSERT INTO `dynamic_content` (`categoryid`,`weight`) VALUES (:categoryid,-1)";
        $stmt = $link->prepare($sql);
        $categoryId = isset($addCategorySuccess)?$result->id:$_POST['categoryid'];
        $stmt->bindValue(':categoryid',$categoryId,PDO::PARAM_INT);
        if (!$stmt->execute()) {
            $result->result = 'KO';
        } else {
            $contentId = $link->lastInsertId();
            if(isset($addCategorySuccess)){
                $result->contentid = $contentId;
            } else {
                $result->id = $contentId;
            }
            $sql = "INSERT INTO `dynamic_content_data` (`contentid`, `lang`, `contentname`, `contentvalue`, `createdby`, `lastmodifiedby`) VALUES (:contentid, :lang, :contentname, :contentvalue, :user, :useragain)";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':contentid',$contentId,PDO::PARAM_INT);
            $stmt->bindValue(':contentname','',PDO::PARAM_STR);
            $stmt->bindValue(':contentvalue','',PDO::PARAM_STR);
            $stmt->bindValue(':lang',$_POST['lang'],PDO::PARAM_STR);
            $stmt->bindValue(':user',$_SESSION['adm_array']['user'],PDO::PARAM_STR);
            $stmt->bindValue(':useragain',$_SESSION['adm_array']['user'],PDO::PARAM_STR);
        }
    } else if (isset($_POST['contentname'])){
        $sql = "UPDATE `dynamic_content_data` SET `contentname` = :contentname, `lastmodifiedby` = :user WHERE `contentid` = :contentid AND `lang` = :lang";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':contentid',$_POST['contentid'],PDO::PARAM_INT);
        $stmt->bindValue(':contentname',$_POST['contentname'],PDO::PARAM_STR);
        $stmt->bindValue(':lang',$_POST['lang'],PDO::PARAM_STR);
        $stmt->bindValue(':user',$_SESSION['adm_array']['user'],PDO::PARAM_STR);
    } else if (isset($_POST['contentvalue'])){
        $sql = "UPDATE `dynamic_content_data` SET `contentvalue` = :contentvalue, `lastmodifiedby` = :user WHERE `contentid` = :contentid AND `lang` = :lang";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':contentid',$_POST['contentid'],PDO::PARAM_INT);
        $stmt->bindValue(':contentvalue',$_POST['contentvalue'],PDO::PARAM_STR);
        $stmt->bindValue(':lang',$_POST['lang'],PDO::PARAM_STR);
        $stmt->bindValue(':user',$_SESSION['adm_array']['user'],PDO::PARAM_STR);
    } else if (isset($_POST['weight'])){
        $sql = "UPDATE `dynamic_content` SET `weight` = :weight WHERE `contentid` = :contentid";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':contentid',$_POST['contentid'],PDO::PARAM_INT);
        $stmt->bindValue(':weight',$_POST['weight'],PDO::PARAM_INT);
    }
}
if (!isset($stmt) || $result->result==='KO' || !$stmt->execute()){
    http_response_code(500);
    if (isset($stmt)){
        error_log("error setting dynamic content".PHP_EOL.var_export($link->errorInfo(),true));
    }
    $link = null;
    header('Content-Type: application/json');
    die (json_encode((object)array('result'=>'KO')));
}
$link = null;
header('Content-Type: application/json');
echo json_encode($result);
