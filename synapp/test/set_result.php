<?php
session_start();
if (!isset($_SESSION['auth'])) {
    die();
}
if ($_SESSION['auth'] !== true) {
    die();
}
require_once dirname(__FILE__) . '/../connect.php';
if (!isset($_SESSION['if_lang'])) {
    $_SESSION['if_lang'] = "eng";
}

if (isset($_POST['ttype'])) {
    $max_test_duration_in_seconds = 0;
    if ($_POST['ttype'] === 'utest') {
        $max_test_duration_in_seconds = SYNAPP_MAX_UTEST_DURATION_IN_SECONDS;
        $langfilelang = file_exists(
            dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/usestxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/usestxt.php';
        $rejected_req = UCTEST_REJECTED_REQUEST;
        $starttimefieldname = 'timer_utestb_start';
        $endtimefieldname = 'timer_utestb_end';
    } elseif ($_POST['ttype'] === 'ctest') {
        $max_test_duration_in_seconds = SYNAPP_MAX_CTEST_DURATION_IN_SECONDS;
        $langfilelang = file_exists(
            '../languages/' . $_SESSION['if_lang'] . '/commonstxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/commonstxt.php';
        $rejected_req = UCTEST_REJECTED_REQUEST;
        $starttimefieldname = 'timer_ctestb_start';
        $endtimefieldname = 'timer_ctestb_end';
    } elseif ($_POST['ttype'] === 'utesta') {
        $max_test_duration_in_seconds = SYNAPP_MAX_UTEST_DURATION_IN_SECONDS;
        $langfilelang = file_exists(
            dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/usestxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/usestxt.php';
        $rejected_req = UCTEST_REJECTED_REQUEST;
        $starttimefieldname = 'timer_utesta_start';
        $endtimefieldname = 'timer_utesta_end';
    } elseif ($_POST['ttype'] === 'ctesta') {
        $max_test_duration_in_seconds = SYNAPP_MAX_CTEST_DURATION_IN_SECONDS;
        $langfilelang = file_exists(
            dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/commonstxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/commonstxt.php';
        $rejected_req = UCTEST_REJECTED_REQUEST;
        $starttimefieldname = 'timer_ctesta_start';
        $endtimefieldname = 'timer_ctesta_end';
    } else {
        die("Error 87364.");
    }
    
    $link = connect();
    if (isset($_POST['bLock'])) {
        if ($_POST['bLock'] == "true") {
            $tlwl = time();
            $_SESSION['user_array'][$endtimefieldname] = $tlwl;
            $sql = "UPDATE users SET {$endtimefieldname} = :endtime WHERE user = :user";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':endtime', $tlwl, PDO::PARAM_INT);
            $stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_STR);
            if ($stmt->execute()===false) {
                error_log("Error updating test ending time {$endtimefieldname} for user {$_SESSION['user_array']['user']}");
                http_response_code(500);
                die ("Internal server error.");
            }
            $sql = "UPDATE test_timings SET end_time = CURRENT_TIMESTAMP WHERE ttypeid = :ttypeid AND tresourceid = :tresourceid AND tresourcelang = :lang AND user = :user";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':ttypeid',$_POST['ttypeid'],PDO::PARAM_INT);
            $stmt->bindValue(':tresourceid',$_POST['tresourceid'],PDO::PARAM_INT);
            $stmt->bindValue(':lang',$_SESSION['if_lang'],PDO::PARAM_STR);
            $stmt->bindValue(':user',$_SESSION['user_array']['user'],PDO::PARAM_STR);
            if ($stmt->execute()===false) {
                error_log (var_export($link->errorInfo(), true));
            }
            $link = null;
            die;
        }
    }
    $sql = "SELECT IFNULL( $starttimefieldname , 0 ) AS dls FROM users WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_STR);
    if ($stmt->execute()===false || !($row = $stmt->fetch(PDO::FETCH_ASSOC))) {
        $link = null;
        die("Error 32423.");
    }
    if (($row['dls'] === '0')) {
        $link = null;
        die();
    } else {
        if (time() > ($row['dls'] + $max_test_duration_in_seconds)) {
            echo $rejected_req;
            $link = null;
            die();
        }
    }
    $time = time();
    
    if (isset($_POST['words']) && $_POST['words']!==''){

        if (isset($_POST['did']) && (!is_string($_POST['did'])||!startsWith($_POST['did'], 'new'))){
            $sql = "UPDATE `test_results` SET
              val = :val
            , lastedit = :lastedit
            WHERE 
            resultid = :did 
            AND user = :user
            AND ttypeid = :ttypeid 
            AND tresourceid = :tresourceid
            AND lang = :lang";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':did',$_POST['did'],PDO::PARAM_INT);
        } else {
            $sql = "INSERT INTO `test_results` 
            ( user , ttypeid , tresourceid , lang , val , firstedit , lastedit ) 
            VALUES 
            ( :user , :ttypeid , :tresourceid , :lang , :val , :firstedit , :lastedit )";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':firstedit',$time,PDO::PARAM_INT);
        }
        $stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_STR);
        $stmt->bindValue(':ttypeid',$_POST['ttypeid'],PDO::PARAM_STR);
        $stmt->bindValue(':tresourceid',$_POST['tresourceid'],PDO::PARAM_STR);
        $stmt->bindValue(':lang',$_SESSION['user_array']['input_language'],PDO::PARAM_STR);
        $stmt->bindValue(':val',$_POST['words'],PDO::PARAM_STR);
        $stmt->bindValue(':lastedit',$time,PDO::PARAM_INT);
        
    } else {
        
        $sql = "DELETE FROM test_results WHERE resultid = :did";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':did', $_POST['did'], PDO::PARAM_INT);
        
    }
   
    if ($stmt->execute() === false) {
        die (var_export($link->errorInfo(), true) . PHP_EOL . $sql);
    }
    $link = null;
    die;
}
$langfilelang = file_exists(
    dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/ditloidstxt.php'
) ? $_SESSION['if_lang'] : 'eng';
/** @noinspection PhpIncludeInspection */
require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/ditloidstxt.php';
$link = connect();
$sql = "SELECT IFNULL(ditloid_lock_timestamp,0) AS dls FROM users WHERE user = :user";
$stmt = $link->prepare($sql);
$stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_STR);
if ($stmt->execute()===false || !($row=$stmt->fetch(PDO::FETCH_ASSOC))) {
    $link = null;
    die("Error 32423.");
}
if (($row['dls'] === '0')) {
    $link = null;
    die();
} else {
    if (time() > $row['dls']) {
        echo DTEST_REJECTED_REQUEST;
        $link = null;
        die();
    }
}
/**
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
foreach ($_POST as $key=>$value) {
    if (startsWith($key,'dwuid')){
        $dwuid = substr($key, strlen('dwuid'));
        $sql = "INSERT INTO `ditloid_values` ( dwuid , user , val ) VALUES ( :dwuid , :user , :val ) ON DUPLICATE KEY UPDATE val = :valagain";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':dwuid', $dwuid, PDO::PARAM_INT);
        $stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_INT);
        $stmt->bindValue(':val', $value, PDO::PARAM_STR);
        $stmt->bindValue(':valagain', $value, PDO::PARAM_STR);
        if ($stmt->execute() === false) {
            die ();
        }
    }
}
if (isset($_POST['bLock'])) {
    if ($_POST['bLock'] == "true") {
        $tlwl = ($row['dls'] - time());
        $_SESSION['user_array']['ditloid_time_left_when_locked'] = $tlwl;
        $sql = "UPDATE `users` SET `ditloid_time_left_when_locked` = :tlwl WHERE `user` = :user";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':tlwl', $tlwl, PDO::PARAM_INT);
        $stmt->bindValue(':user', $_SESSION['user_array']['user']);

        if ($stmt->execute() === false) {
            error_log(var_export($link->errorInfo(), true));
            die("Error performing database operation.");
        }
        
    }
}
$link = null;
