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

if (isset($_POST['create']) && $_POST['create']==='true') {
    $stmt = $link->query("SELECT MAX(`tresourceid`) tresourceid FROM `test_resources`");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $tresourceid = $row['tresourceid'] + 1;
    foreach ($_SESSION['interface_languages'] as $language){
        $sql = "INSERT INTO `test_resources` (`tresourceid`, `tresourcelang`, `tresourcedesc`) VALUES (:tresourceid, :tresourcelang, :tresourcedesc)";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':tresourceid', $tresourceid, PDO::PARAM_STR);
        $stmt->bindValue(':tresourcelang', $language['val'], PDO::PARAM_STR);
        $stmt->bindValue(':tresourcedesc', '', PDO::PARAM_STR);
        if ($stmt->execute() === false) {
            error_log("Error: " . var_export($link->errorInfo(), true));
            http_response_code(500);
            $output = array('result'=>'KO', 'error'=>'Error creating test resource.');
        } else {
            $output = array('result' => 'OK');
        }
    }
} else if (isset($_POST['tresourceid'])&&isset($_POST['ttypeid'])){
    $tresourceid = $_POST['tresourceid'];
    if (isset($_POST['deleteEntry']) && $_POST['deleteEntry']==='true'){
        $sql = "DELETE FROM `test_type_resources` WHERE `ttypeid` = :ttypeid AND `tresourceid` = :tresourceid";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':ttypeid',$_POST['ttypeid'], PDO::PARAM_INT);
        $stmt->bindValue(':tresourceid',$_POST['tresourceid'], PDO::PARAM_INT);
        if ($stmt->execute() === false) {
            error_log("Error: " . var_export($link->errorInfo(), true));
            http_response_code(500);
            $output = array('result'=>'KO', 'error'=>'Error storing test resource data.');
        } else {
            $output = array('result' => 'OK');
        }
    } else {
        foreach ($_SESSION['interface_languages'] as $language) {
            $sql = "INSERT INTO `test_type_resources` (`ttypeid`, `tresourceid`, `tresourcelang`) VALUES (:ttypeid, :tresourceid, :tresourcelang)";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':ttypeid', $_POST['ttypeid'], PDO::PARAM_INT);
            $stmt->bindValue(':tresourceid', $_POST['tresourceid'], PDO::PARAM_INT);
            $stmt->bindValue(':tresourcelang', $language['val'], PDO::PARAM_STR);
            if ($stmt->execute() === false) {
                error_log("Error: " . var_export($link->errorInfo(), true));
                http_response_code(500);
                $output = array('result' => 'KO', 'error' => 'Error storing test resource associated type data.');

            } else {
                $output = array('result' => 'OK');
            }
        }
    }
} else if (isset($_POST['tresourceid'])&&isset($_POST['tresourcelang'])&&isset($_POST['tresourcedesc'])){
    $sql = "UPDATE `test_resources` SET `tresourcedesc` = :tresourcedesc WHERE `tresourceid` = :tresourceid AND `tresourcelang` = :tresourcelang";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':tresourceid', $_POST['tresourceid'], PDO::PARAM_INT);
    $stmt->bindValue(':tresourcelang', $_POST['tresourcelang'], PDO::PARAM_STR);
    $stmt->bindValue(':tresourcedesc', $_POST['tresourcedesc'], PDO::PARAM_STR);
    if ($stmt->execute() === false) {
        error_log("Error: " . var_export($link->errorInfo(), true));
        http_response_code(500);
        $output = array('result' => 'KO', 'error' => 'Error storing test resource description data.');
    } else {
        $output = array('result' => 'OK');
    }
}

$link = null;

header('Content-Type: application/json');
echo json_encode($output);
