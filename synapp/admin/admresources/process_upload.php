<?php
session_start();
if ( !isset($_SESSION['adm']) || $_SESSION['adm'] !== true ) {
    header("Location: ../admlogin.phtml?location=admresources.phtml");
    die();
}
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../../account/'.SYNAPP_CONFIG_DIRNAME.'/profile_constants_constraints_defaults_and_selector_values.php';
require_once dirname(__FILE__) . '/../../connect.php';
require_once dirname(__FILE__) . '/../../utils/http_response_code.php';
/**
 * @param array $file
 * @return boolean|array
 */
function process_upload($file)
{

    if (!is_file($file['tmp_name'])) {

        return false;

    } else {

        if (
            (
                ($file['type'] == "image/x-png")
                || ($file['type'] == "image/png")
                || ($file['type'] == "image/jpeg")
                || ($file['type'] == "image/jpg")
                || ($file['type'] == "image/pjpeg")
                || ($file['type'] == "image/gif")
                || ($file['type'] == "image/webp")
            )
            && ($file['size'] < MAX_IMAGE_SIZE_BYTES)
        ) {

            if ($file['error'] > 0) {

                return array('result'=>'KO','error'=>"error1");

            } else {

                $extension = str_replace('x-', '', str_replace('image/', '', $file['type']));
                $newfilename = hash_file('sha256', $file['tmp_name']) . '.' . $extension;
                move_uploaded_file(
                    $file['tmp_name'],
                    dirname(__FILE__) . "/../../uploads/images/" . $newfilename
                );
                return array('result'=>'OK','filename'=>$newfilename, 'oldname'=>$file['name']);

            }

        } else {

            return array('result'=>'KO','error'=>"error2"); //("Avatar Error: MAX 290KB, gif/png/jpeg<br />");

        }

    }

}

// 'images' -> file input name attribute
if (empty($_FILES['images'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(array('result'=>'KO','error'=>'No files found for upload.'));
    exit;
}

// get the files posted
$images = $_FILES['images'];
$filenames = $images['name'];

$result = false;

$link = connect();

$ids = array();
for($i=0; $i < count($filenames); $i++){
    $file = array('name'=>$images['name'][$i],'type'=>$images['type'][$i],'tmp_name'=>$images['tmp_name'][$i],'error'=>$images['error'][$i],'size'=>$images['size'][$i]);
    $result = process_upload($file);
    if ($result === false || isset($result['error'])){
        break;
    }
    $ids[] = $result['filename'];
}
$output = array();
if ($result!==false && !isset($result['error'])) {

    // if a collection name is defined, assume creation (we do not allow edition of collection names)
    if (isset($_POST['collection_name'])) {
        $sql = "INSERT INTO `image_collections` (`collectionname`) VALUES (:collectionname)";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':collectionname', $_POST['collection_name'], PDO::PARAM_STR);
        $stmt->execute();
        $collectionId = $link->lastInsertId();
    } else {
        $collectionId = $_POST['collectionid'];
    }

    $sql = "INSERT INTO images (id, name, collectionid, uploader) VALUES ";
    for ($i=0;$i<count($ids);$i++) {
        $sql .= "(:id{$i}, :name{$i}, :collectionid{$i}, :uploader{$i}),";
    }
    $sql = rtrim($sql,',');
    $sql .= " ON DUPLICATE KEY UPDATE name = VALUES(name), collectionid = VALUES(collectionid), uploader = VALUES(uploader)";
    $stmt = $link->prepare($sql);
    for ($i=0;$i<count($ids);$i++) {
        $stmt->bindValue(":id{$i}", $ids[$i], PDO::PARAM_STR);
        $stmt->bindValue(":name{$i}", $filenames[$i], PDO::PARAM_STR);
        $stmt->bindValue(":collectionid{$i}", $collectionId, PDO::PARAM_STR);
        $stmt->bindValue(":uploader{$i}", $_SESSION['adm_array']['user'], PDO::PARAM_STR);
    }
    if ($stmt->execute() === false) {

        http_response_code(500);
        error_log("Error: " . var_export($link->errorInfo(), true));
        $output = array('result'=>'KO','error'=>'Error storing image metadata.');

    }

} elseif ($result===false){
    http_response_code(500);
    error_log("Couldn't process upload");
    $output = array('result'=>'KO','error'=>'No files were processed.');
} elseif ($result['error'] === "error1") {
    http_response_code(500);
    $output = array('result'=>'KO','error'=>'Unknown error uploading image file.');
} elseif ($result['error'] === "error2") {
    http_response_code(400);
    $output = array('result'=>'KO','error'=>"Image Error: MAX " . (MAX_IMAGE_SIZE_BYTES / 1024) . " KB, gif/png/jpeg");
}

$link = null;

header('Content-Type: application/json');
echo json_encode($output);
