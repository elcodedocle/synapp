<?php
session_start();
if (!isset($_SESSION['auth'])) {
    die("Unauthenticated Session.");
}
if ($_SESSION['auth'] !== true) {
    die("Unauthenticated Session.");
}
if (!isset($_SESSION['if_lang'])) {
    $_SESSION['if_lang'] = "eng";
}
if (!isset($_GET['ttype'])) {
    die("Unfortunate error. Guru Stack ref. 987341.b");
}
if ($_GET['ttype'] == 'dtest') {
    $langfilelang = file_exists(
        dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/ditloidstxt.php'
    ) ? $_SESSION['if_lang'] : 'eng';
    /** @noinspection PhpIncludeInspection */
    require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/ditloidstxt.php';
    $location = 'test_will_shortz_morgan_worthy_ditloid.phtml?';
    $check1 = ($_SESSION['user_array']['gotestafter'] == '0');
    $check2 = ($_SESSION['user_array']['gotestbefore'] == '0');
    $title = DTEST_START_COUNTER_TITLE;
    $cconfirm = DTEST_CONFIRM_CONTINUE;
    $bconfirm = DTEST_CONFIRM_CONTINUE_BEFORE;
    $wait = DTEST_ALERT_WAIT;
} else {
    if ($_GET['ttype'] == 'utest') {
        $langfilelang = file_exists(
            dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/usestxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/usestxt.php';
        $location = 'uctest.phtml?ttype=utest&';
        $check1 = ($_SESSION['user_array']['timer_utestb_start'] == 0);
        $check2 = false;
    } elseif ($_GET['ttype'] == 'ctest') {
        $langfilelang = file_exists(
            dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/commonstxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/commonstxt.php';
        $location = 'uctest.phtml?ttype=ctest&';
        $check1 = ($_SESSION['user_array']['timer_ctestb_start'] == 0);
        $check2 = false;
    } elseif ($_GET['ttype'] == 'utesta') {
        $langfilelang = file_exists(
            dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/usestxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/usestxt.php';
        $location = 'uctest.phtml?ttype=utesta&';
        $check1 = ($_SESSION['user_array']['gotestafter'] == '0');
        $check2 = ($_SESSION['user_array']['timer_utesta_start'] == 0);
    } elseif ($_GET['ttype'] == 'ctesta') {
        $langfilelang = file_exists(
            dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/commonstxt.php'
        ) ? $_SESSION['if_lang'] : 'eng';
        /** @noinspection PhpIncludeInspection */
        require_once dirname(__FILE__) . '/../languages/' . $langfilelang . '/commonstxt.php';
        $location = 'uctest.phtml?ttype=ctesta&';
        $check1 = ($_SESSION['user_array']['gotestafter'] == '0');
        $check2 = ($_SESSION['user_array']['timer_ctesta_start'] == 0);
    } else {
        die ("Unfortunate error. Guru proverb says: 0100110");
    }
    $title = UCTEST_START_COUNTER_TITLE;
    $cconfirm = UCTEST_CONFIRM_CONTINUE;
    $bconfirm = UCTEST_CONFIRM_CONTINUE_BEFORE;
    $wait = UCTEST_ALERT_WAIT;
}
echo "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><title>" . $title . "</title><script type='text/javascript'>";
if ((($check1) && ($check2))) {
    echo "
    alert(\"" . $wait . "\");
    window.history.back();";
} else {
    $confirm = ($check2) ? $cconfirm : $bconfirm;
    echo "
    var bContinue=confirm(\"" . $confirm . "\");
    if (bContinue) {window.location='../" . $location . "start=true';} else {window.location='../profile.phtml';}";
}
echo "</script></head><body></body></html>";
