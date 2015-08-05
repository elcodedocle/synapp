<?php
session_start();
if (!isset($_SESSION['auth'])) {
    header("Location: ../login.phtml");
    die();
}
if ($_SESSION['auth'] !== true) {
    header("Location: ../login.phtml");
    die ();
}
if (!isset($_GET['type'])) {
    header("Location: ../watrix.phtml");
    die();
}

require_once dirname(__FILE__) . '/../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../connect.php';
require_once dirname(__FILE__) . '/get_evaluation_pair.php';
require_once dirname(__FILE__) . '/get_pair.php';
if (!isset($_SESSION['if_lang'])) {
    $_SESSION['if_lang'] = "eng";
}
$langfilelang = file_exists(
    dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/ditloidstxt.php'
) ? $_SESSION['if_lang'] : 'eng';
/** @noinspection PhpIncludeInspection */
require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/finishtxt.php';

$link = connect();

$title2 = "";
$target_mode = "";

/* check if there are any associations left in watrix, watrix_flash mode or both if comming from supervisor... */
for ($type = ($_GET['type'] === 3) ? 1 : ($_GET['type'] % 2) + 1; (($type != $_GET['type']) && ($type < 3)); $type++) {
    if (get_pair($link, $type) !== false) {
        /*  ...and redirect to the corresponding mode if there are */
        if ($type === 2) {
            $target = "watrix_flash.phtml";
            $target_mode = END_TARGET_MODE_FLASH;
            $title2 = END_TITLE_2_FLASH;
        } else {
            $target = "watrix.phtml";
            $target_mode = END_TARGET_MODE_STANDARD;
            $title2 = END_TITLE_2_STANDARD;
        }
    }
}
if (!isset($target)) {
    /* check if the ditloid test has been done yet and redirect to it if not */
    if (($_SESSION['user_array']['gotestbefore'] != "1") && ((($_SESSION['user_array']['ditloid_lock_timestamp'] > time(
                )) && ($_SESSION['user_array']['ditloid_time_left_when_locked'] == 0))) || ($_SESSION['user_array']['ditloid_lock_timestamp'] == 0)
    ) {
        $target = "test_will_shortz_morgan_worthy_ditloid.phtml";
        $target_mode = END_TARGET_MODE_DTEST;
        $title2 = END_TITLE_2_DTEST;
        if ($_SESSION['user_array']['gotestafter'] != "1") {
            $_SESSION['user_array']['gotestafter'] = "1";
            $sql = "UPDATE users SET gotestafter = 1 WHERE user = :user";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_STR);
            if ($stmt->execute()===false){
                error_log("Couldn't update gotestafter field for user {$_SESSION['user_array']['user']}");
            }
        }
    }
}
/* check if there are any answers awaiting evaluation from this user and redirect to supervisor mode if true */
if (($_GET['type'] !== 3) && (!isset($target))) {
    if (get_evaluation_pair($link) !== false) {
        $target = "supervisor.phtml";
        $target_mode = END_TARGET_MODE_SUPERVISOR;
        $title2 = END_TITLE_2_SUPERVISOR;
    }
}

/* in case the test's been done and there are also no evaluations or associations left to do, redirect to the stats page */
if (!isset($target)) {
    $target = "stats.phtml";
    $target_mode = END_TARGET_MODE_STATS;
    $title2 = END_TITLE_2_STATS;
}
$link = null;
$target_path = '../';
if (isset($_SESSION['justlogged'])) {
    if ($_SESSION['justlogged'] === true) {
        $_SESSION['justlogged'] = false;
        header("Location: " . $target_path . $target);
        die();
    }
}

/* Begin the html output */
echo "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><title>" . END_TITLE_1 . " " . $title2 . "</title>
<link href='..".SYNAPP_UI_RESOURCES_PATH."/style/dist/consolidated.min.css' rel='stylesheet' type='text/css' />
<style type='text/css'>
img { max-width:100%; }
#content{
    border:1px solid #000;
    width:468px;
    padding:0px 0px 20px 0px;
    position:relative;
    text-align:center;
    left:-50%;
}
</style>
<!--[if IE 7]>
<style type='text/css'>
#wrapper{
position:relative;
overflow:hidden;
}
</style>
<![endif]-->
<!--[if lt IE 8]>
<style type='text/css'>
#wrap {top:50%}
#content{top:-50%;}
</style>
<![endif]-->
<script type='text/javascript'>
var dotsCount=1;
var ready = false;
var xmlhttp;
function loadDots(){
    if ((dotsCount>=3)&&ready) {
        var stateObj = { foo: 'stats' };
        window.history.pushState(stateObj, 'Title', '" . $target_path . $target . "');
        document.open('text/html');
        document.write(xmlhttp.responseText);
        document.close();
        return;
    }
    if (dotsCount%3!=0){
        dotspace = '.';
        if ((dotsCount%3)==2){ dotspace+='.'; } else { dotspace+='&nbsp;'; }
        document.getElementById('loadingDots').innerHTML=dotspace;
        document.title+='.';
    } else {
        document.getElementById('loadingDots').innerHTML='';
        document.title=document.title.replace(/\\.+$/,'.  ');
    }
    setTimeout('loadDots()',1000);
    dotsCount++;
}
</script>
</head>
<body>";
echo "<div id='wrapper'><div id='outer'><div id='wrap'><div id='content'>
<h1>" . END_HEADER . "</h1>";
echo "<h2>Loading " . $target_mode . ".<span id='loadingDots'>&nbsp;&nbsp;</span></h2>" . PHP_EOL;
if (($target === 'stats.phtml') && ($_GET['type'] === "3")) {
    echo "<p>" . END_PARAGRAPH_1_B . "</p>" . PHP_EOL;
}
echo "
</div></div></div></div>
<script type='text/javascript'>
window.onload=function() {
    setTimeout('loadDots()',1000);
    if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
      } else {// code for IE6, IE5
          xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
      }

    xmlhttp.onreadystatechange=function(){
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
               if (xmlhttp.responseText) {
                       ready=true;                    
               }
        }
      };
      
    xmlhttp.open('GET','" . $target_path . $target . "',true);
    xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xmlhttp.send();
};
</script></body></html>";
