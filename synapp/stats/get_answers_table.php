<?php
if (function_exists('session_status')){
    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }
} else if (session_id() == '') {
    session_start();
}
if ((!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) && (!isset($_SESSION['adm'])||$_SESSION['adm']!==true)) {
    die ("Unauthenticated session.");
}
$bAdm = isset($_SESSION['adm'])?$_SESSION['adm']:false;
$userArray = isset($_SESSION['user_array'])?$_SESSION['user_array']:$_SESSION['adm_array'];
if (!isset($_REQUEST['orderby'])) {
    die ("No order set.");
}
if (!isset($_REQUEST['orderbytype'])) {
    die ("No order type set.");
}
$t = $_REQUEST['orderbytype'];
require_once dirname(__FILE__) . '/../connect.php';
require_once dirname(__FILE__) . '/../account/parsers.php';
require_once dirname(__FILE__) . '/get_test_results.php';
$link = connect();
if (isset($_GET['user'])) {
    if (parse($_GET['user']) !== 0) {
        die("Error: Not authorized.");
    }
    if (!$bAdm && ($_GET['user'] != $userArray['user']) && ($t !== 'associations')) {
        die ("Function not active yet.");
    }
    $user = $_GET['user'];
    $sql = "SELECT input_language,working_group,hstats,hprofile FROM users WHERE user=:user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user',$user,PDO::PARAM_STR);
    if ($stmt->execute() === false || $stmt->rowCount() < 1) {
        die("Error: Not authorized.");
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$bAdm && (((ord($row['hprofile']) !== 0) || (ord($row['hstats']) !== 0)) && (strtolower($user) !== strtolower(
                $userArray['user']
            )))
    ) {
        die("Error: Not authorized.");
    }
    $input_language = $row['input_language'];
    $working_group = $row['working_group'];
} else {
    $user = $_SESSION['user_array']['user'];
    $input_language = $_SESSION['user_array']['input_language'];
    $working_group = $_SESSION['user_array']['working_group'];
}
if (!isset($_SESSION['orderby'][$t])) {
    $_SESSION['orderby'][$t][0][0] = "allpointszerotenscale";
    $_SESSION['orderby'][$t][0][1] = "orgpointszerotenscale";
    $_SESSION['orderby'][$t][0][2] = "intpointszerotenscale";
    $_SESSION['orderby'][$t][0][3] = "word";
    $_SESSION['orderby'][$t][0][4] = "type";
    $_SESSION['orderby'][$t][0][5] = "upvotes";
    $_SESSION['orderby'][$t][0][6] = "downvotes";
    $_SESSION['orderby'][$t][0][7] = "neutralvotes";
    $_SESSION['orderby'][$t][0][8] = "popupvotes";
    $_SESSION['orderby'][$t][0][9] = "popdownvotes";
    $_SESSION['orderby'][$t][0][10] = "popneutralvotes";
    $_SESSION['orderby'][$t][0][11] = "time_stamp";
    foreach ($_SESSION['orderby'][$t][0] as $i => $el) {
        $_SESSION['orderby'][$t][1][$i] = "DESC";
    }
    $_SESSION['orderby'][$t][1][3] = "ASC";
    $_SESSION['orderby'][$t][1][4] = "ASC";
} else {
    switch ($_REQUEST['orderby']) {
        case 'answer':
            $pushorderby = "word";
            break;
        case 'points':
            $pushorderby = "allpointszerotenscale";
            break;
        case 'orgpoints':
            $pushorderby = "orgpointszerotenscale";
            break;
        case 'intpoints':
            $pushorderby = "intpointszerotenscale";
            break;
        case 'type':
            $pushorderby = "type";
            break;
        case 'upvotesint':
            $pushorderby = "upvotes";
            break;
        case 'downvotesint':
            $pushorderby = "downvotes";
            break;
        case 'neutralvotesint':
            $pushorderby = "neutralvotes";
            break;
        case 'upvotescre':
            $pushorderby = "popupvotes";
            break;
        case 'downvotescre':
            $pushorderby = "popdownvotes";
            break;
        case 'neutralvotescre':
            $pushorderby = "popneutralvotes";
            break;
        case 'timestamp':
            $pushorderby = "time_stamp";
            break;
    }
    if (isset($pushorderby)) {
        if ($pushorderby === $_SESSION['orderby'][$t][0][0]) {
            $_SESSION['orderby'][$t][1][0] = ($_SESSION['orderby'][$t][1][0] === "DESC") ? "ASC" : "DESC";
        } else {
            $limit = array_search($pushorderby, $_SESSION['orderby'][$t][0]);
            $pushorderbyascdesc = $_SESSION['orderby'][$t][1][$limit];
            for ($i = $limit - 1; $i >= 0; $i--) {
                $_SESSION['orderby'][$t][0][$i] = $_SESSION['orderby'][$t][0][$i + 1];
                $_SESSION['orderby'][$t][1][$i] = $_SESSION['orderby'][$t][1][$i + 1];
            }
            $_SESSION['orderby'][$t][0][0] = $pushorderby;
            $_SESSION['orderby'][$t][1][0] = $pushorderbyascdesc;
        }
    }
}

$joined = array();

foreach ($_SESSION['orderby'][$t][0] as $i => $el) {
    $joined[] = $el . ' ' . $_SESSION['orderby'][$t][1][$i];
}

$orderby = implode($joined, ",");

if ($t == 'associations') {
    $sql = "SELECT 
          `associd`
        , `id1`
        , `id2`
        , `assigned_group`
        , `user`
        , `lang`
        , `word`
        , `time_stamp`
        , `type`
        , `cohfullvotes` upvotes
        , `cohhalfvotes` neutralvotes
        , `cohzerovotes` downvotes
        , `orgfullvotes` popupvotes
        , `orghalfvotes` popneutralvotes
        , `orgzerovotes` popdownvotes
        , ROUND(
            (
                (
                      IFNULL(cohfullvotes,0)
                    + IFNULL(cohhalfvotes,0) * 0.5
                    + IFNULL(orgfullvotes,0)
                    + IFNULL(orghalfvotes,0) * 0.5
                )
                /
                IFNULL(
                    NULLIF(
                          IFNULL(cohfullvotes,0)
                        + IFNULL(cohzerovotes,0)
                        + IFNULL(cohhalfvotes,0)
                        + IFNULL(orgfullvotes,0)
                        + IFNULL(orgzerovotes,0)
                        + IFNULL(orghalfvotes,0)
                        , 0
                    )
                    , 1
                )
            ) * 10
            , 2
        ) AS allpointszerotenscale
        , ROUND(
            (
                (
                      IFNULL(cohfullvotes,0)
                    + IFNULL(cohhalfvotes,0) * 0.5
                )
                /
                IFNULL(
                    NULLIF(
                          IFNULL(cohfullvotes,0)
                        + IFNULL(cohzerovotes,0)
                        + IFNULL(cohhalfvotes,0)
                        , 0
                    )
                    , 1
                )
            ) * 10
            , 2
        ) AS intpointszerotenscale
        , ROUND(
            (
                (
                      IFNULL(orgfullvotes,0)
                    + IFNULL(orghalfvotes,0) * 0.5
                )
                /
                IFNULL(
                    NULLIF(
                          IFNULL(orgfullvotes,0)
                        + IFNULL(orgzerovotes,0)
                        + IFNULL(orghalfvotes,0)
                        , 0
                    )
                    , 1
                )
            ) * 10
            , 2
        ) AS orgpointszerotenscale
        FROM `vassociations` 
        WHERE 
            user = :user
            AND assigned_group = :working_group
            AND lang = :input_language
        ORDER BY {$orderby}";
    
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user',$user,PDO::PARAM_STR);
    $stmt->bindValue(':working_group',$working_group,PDO::PARAM_STR);
    $stmt->bindValue(':input_language',$input_language,PDO::PARAM_STR);
    $stmt->execute();
    
} else {
    
    $stmt = get_test_results($link, $user, $input_language, $orderby);
    
}

echo build_html_answers_table($t, $user, $stmt);

/**
 * Builds html answers table
 *
 * @param string $t table type
 * @param string $user user name
 * @param \PDOStatement $stmt answers query result
 * @param null|string $requestUser user formulating the request
 * @return string the html table
 */
function build_html_answers_table($t, $user, $stmt, $requestUser = null){
    if ($requestUser === null){
        $requestUser = isset($_SESSION['user_array'])?$_SESSION['user_array']['user']:$_SESSION['adm_array']['user'];
    } 
    if ($stmt === false || $stmt === null){
        return "";
    }
    $out = "";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $out .= "<tr><td>";
        if ($t == 'associations') {
            $out .= (!isset($_SESSION['adm']) && strtolower($user) !== strtolower(
                    $requestUser
                )) ? $row['word'] : "<a href='#' onclick=\"javascript:detail=window.open(&quot;show_association.phtml?user=" . urlencode(
                    $user
                )
                . "&id1=" . urlencode($row['id1']) . "&id2=" . urlencode($row['id2']) . "&word=" . urlencode(
                    $row['word']
                ) . "&type=" . urlencode($row['type'])
                . "&quot;, 'STATS','location=0,status=0,scrollbars=0,resizable=0,width=880,height=440');setTimeout('detail.focus()', 1);return false;\">"
                . $row['word'] . "</a>";
        } else {
            $out .= $row['word'];
        }
        $out .= "</td>" . "<td>";
        $out .= $_REQUEST['type_' . $row['type']] . "</td>"
            . "<td>" . $row['intpointszerotenscale'] . '/' . $row['orgpointszerotenscale'] . ' (' . $row['allpointszerotenscale'] . ')' . "</td>"
            . "<td>" . $row['upvotes'] . "/" . $row['popupvotes'] . "</td>"
            . "<td>" . $row['neutralvotes'] . "/" . $row['popneutralvotes'] . "</td>"
            . "<td>" . $row['downvotes'] . "/" . $row['popdownvotes'] . "</td>"
            . "<td>" . $row['time_stamp'] . "</td>"
            . "</tr>" . PHP_EOL;
    }
    return $out;
}
