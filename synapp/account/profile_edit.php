<?php

require_once dirname(__FILE__) . '/config/deployment_environment.php';
require_once dirname(
        __FILE__
    ) . '/' . SYNAPP_CONFIG_DIRNAME . '/profile_constants_constraints_defaults_and_selector_values.php';
require_once dirname(__FILE__) . '/process_avatar.php';
require_once dirname(__FILE__) . '/send_confirmation_email.php';

/**
 * @param string $user
 * @param string $old
 * @param string $pass
 * @param PDO $link
 * @return bool
 */
function change_pass($user, $old, $pass, $link)
{

    $sql = "SELECT pass, last_login FROM users WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user);

    $use_password_verify =
        defined('SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION')
        && (
            SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION === true
            ||
            is_string(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)
            && (trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === 'on'
                || trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === 'true'
                || trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === '1')
        ) ?
            true : false;

    if ($stmt->execute()!== false && $ua = $stmt->fetch(PDO::FETCH_ASSOC)) {

        if (($ua['last_login'] >= PRENORAINBOW_TIMESTAMP) && (!$use_password_verify || $ua['last_login'] < PRENOPHASH_TIMESTAMP)
            && $ua['pass'] !== hash("sha256", $old . NORAINBOW_SALT)
            || $ua['last_login'] < PRENORAINBOW_TIMESTAMP && $ua['pass'] !== hash(
                "sha256",
                hash("sha256", $old) . NORAINBOW_SALT
            )
            || $use_password_verify && $ua['last_login'] >= PRENOPHASH_TIMESTAMP && !password_verify($old, $ua['pass'])
        ) {

            return false;

        }

    } else {

        $link = null;
        die ("Error: User not found.");

    }

    $recovery = hash("sha256", mt_rand());
    if ($use_password_verify) {
        $hashedPassword = password_hash($pass, SYNAPP_PASSWORD_DEFAULT);
    } else {
        $hashedPassword = hash("sha256", $pass . NORAINBOW_SALT);
    }
    $sql = "UPDATE users SET pass = :hashedpass, recovery = :recovery WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':hashedpass', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindValue(':recovery', $recovery, PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    
    if ($stmt->execute() === false) {

        die ("Error: " . var_export($link->errorInfo(), true));

    }

    return true;

}

/**
 * @param string $edit
 * @param \PDO $link
 */
function change_pass_form($edit, $link)
{

    $passautherr = false;
    $passoldmatcherr = false;
    $passwordmatcherr = false;
    $passwordusermatcherr = false;
    $error = -1;

    if (count($_POST)) {

        $user = $_POST['user'];

        if ($_POST['edit'] === $edit) {

            $old = $_POST['old'];
            $pass = $_POST['pass'];
            $pass2 = $_POST['pass2'];

            if ($pass != $user) {

                if ($pass === $pass2) {

                    if ($pass !== $old) {

                        $error = parse($pass, 10, 64);

                        if ($error === 0) {

                            if (change_pass($user, $old, $pass, $link)) {

                                echo "Password: ********** [<a href=\"profile.phtml?user=" .
                                    $user . "&edit=password\">" . PR_EDIT . "</a>] <span style=color:red>" . PR_DONE_1 . "</span><br />" . PHP_EOL;
                                $_SESSION['user_array']['pass'] = hash("sha256", $pass);

                                return;

                            } else {
                                $passautherr = true;
                            }
                        }

                    } else {

                        $passoldmatcherr = true;

                    }
                } else {

                    $passwordmatcherr = true;

                }
            } else {

                $passwordusermatcherr = true;

            }

        }

    }

    echo "<form id=\"editForm\" method=\"POST\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"edit\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" id=\"focusId\" name=\"focusId\" value=\"old\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"user\" value=\"" . $_SESSION['user_array']['user'] . "\">" . PHP_EOL;
    echo "<table><tr><td style='text-align:right;'>" . PR_PASS_OLD . "</td><td><input type=\"password\" name=\"old\"></td></tr>" . PHP_EOL;
    echo "<tr><td style='text-align:right;'>" . PR_PASS . "</td><td><input type=\"password\" name=\"pass\"></td></tr>" . PHP_EOL;
    echo "<tr><td style='text-align:right;'>" . PR_REPASS . "</td><td><input type=\"password\" name=\"pass2\"></td></tr>" . PHP_EOL;
    echo "<tr><td></td>" . PHP_EOL;
    echo "<td style='text-align:right;'><input type=\"submit\" value=\"" . PR_SEND . "\"></td></tr></table>" . PHP_EOL;
    echo "</form>" . PHP_EOL;
    echo "<p style=color:red>";

    if ($passwordusermatcherr) {

        echo PR_ERR_PUMATCH . "<br />" . PHP_EOL;

    }

    if ($passautherr) {

        echo PR_ERR_PAUTH . "<br />" . PHP_EOL;

    }

    if ($passwordmatcherr) {

        echo PR_ERR_PMATCH . "<br /><script type='text/javascript'>focusId='pass2'</script>" . PHP_EOL;

    }

    if ($passoldmatcherr) {

        echo PR_ERR_OLDPMATCH . "<br />" . PHP_EOL;

    }

    switch ($error) {

        case 1:
            echo PR_ERR_EMPTY . "<br />" . PHP_EOL;
            break;

        case 2:
            echo PR_ERR_PMIN . "<br /><script type='text/javascript'>focusId='pass'</script>" . PHP_EOL;
            break;

        case 3:
            echo PR_ERR_PMAX . "<br /><script type='text/javascript'>focusId='pass'</script>" . PHP_EOL;
            break;

        case 4:
            echo PR_ERR_PFORMAT . "<br /><script type='text/javascript'>focusId='pass'</script>" . PHP_EOL;
            break;

    }

    echo "</p>";

}

/**
 * @param string $edit
 * @param PDO $link
 * @return bool
 */
function change($edit, $link)
{

    if (count($_POST)) {

        $user = $_POST['user'];

        if ($_POST['edit'] == $edit) {

            $hidden = (isset($_POST['h' . $edit])) ? 1 : 0;
            $val = ($edit === "birthday") ? $_POST['birthdate_year'] . "-" . $_POST['birthdate_month'] . "-01" : $_POST[$edit];
            $set = "`" . str_replace("`","``",$edit) . "` = :val, `" . str_replace("`","``","h" . $edit) . "` = b'$hidden'";

            if ($edit === "input_language") {

                /* joining iflang and inlang */
                $set .= ", interface_language = :valagain, hinterface_language = b'$hidden'";

            }

            $sql = "UPDATE users SET " . $set . " WHERE user = :user";
            
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':val', $val, PDO::PARAM_STR);
            $stmt->bindValue(':user', $user, PDO::PARAM_STR);
            if ($edit === "input_language") {
                $stmt->bindValue(':valagain', $val, PDO::PARAM_STR);
            }
            if ($stmt->execute() === false) {

                die ("Error: " . var_export($link->errorInfo(), true));

            }

            $_SESSION['user_array'][$edit] = $val;
            $_SESSION['user_array']["h" . $edit] = chr($hidden);

            if ($edit === "interface_language") {
                $_SESSION['if_lang'] = $val;
            }

            if ($edit === "input_language") {

                /* joining iflang and inlang */
                $_SESSION['user_array']['interface_language'] = $val;
                $_SESSION['user_array']['hinterface_language'] = chr($hidden);
                $_SESSION['if_lang'] = $val;

            }

            return true;

        }

        return false;

    }

    return false;

}

/**
 * @param string $str
 * @param string $edit
 * @param \PDO $link
 * @return bool
 */
function change_text($str, $edit, $link)
{

    if (change($edit, $link)) {

        return true;

    }

    echo "<form id=\"editForm\" method=\"POST\">" . $str . ": <input type=\"text\" id=\"" . $edit . "\" name=\"" . $edit
        . "\" value=\"" . $_SESSION['user_array'][$edit] . "\" />";
    echo "<input type=\"hidden\" id=\"focusId\" name=\"focusId\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"edit\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"user\" value=\"" . $_SESSION['user_array']['user'] . "\">" . PHP_EOL;
    echo "<input type=\"checkbox\" name=\"h" . $edit . "\" value=\"true\"";

    if (ord($_SESSION['user_array']['h' . $edit])) {

        echo " checked=\"checked\" ";

    }

    echo '/>' . PR_HIDE . '<br /></form>' . PHP_EOL;

    return false;

}

/**
 * @param string $str
 * @param string $edit
 * @param \PDO $link
 * @return bool
 */
function change_gender($str, $edit, $link)
{

    if (change($edit, $link)) {

        return true;

    }

    echo "<form method=\"POST\" id=\"editForm\">" . $str . ": <input type=\"radio\" name=\"" . $edit . "\" value=\"M\" />"
        . PR_MALE . " "
        . "<input type=\"radio\" name=\"" . $edit . "\" value=\"F\" />" . PR_FEMALE . " ";
    echo "<input type=\"hidden\" name=\"edit\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"user\" value=\"" . $_SESSION['user_array']['user'] . "\">" . PHP_EOL;
    echo "<input type=\"checkbox\" name=\"h" . $edit . "\" value=\"true\"";

    if (ord($_SESSION['user_array']['h' . $edit])) {
        echo " checked=\"checked\" ";
    }

    echo '/> ' . PR_HIDE;
    echo "[<a href=\"#\" onclick=\"javascript:document.getElementById('editForm').submit();return false;\">"
        . PR_SEND . "</a>]<br /></form>";

    return false;

}

/**
 * @param string $str
 * @param string $edit
 * @param \PDO $link
 * @return bool
 */
function change_birthday($str, $edit, $link)
{

    if (change($edit, $link)) {

        return true;

    }

    $maxyear = date('Y');
    $years = array();
    $months = array();

    for ($i = $maxyear; $i >= LOWEST_ALLOWED_BIRTHYEAR; $i--) {

        $years[$maxyear - $i]['name'] = $i;
        $years[$maxyear - $i]['val'] = $i;

    }

    for ($i = 1; $i <= 12; $i++) {

        $months[$i - 1]['name'] = strftime('%B', strtotime('2012-' . $i . '-27'));
        $months[$i - 1]['val'] = $i;

    }

    $birthdate_year_options = option_list($years, strftime('%Y', strtotime($_SESSION['user_array']['birthday'])));
    $birthdate_month_options = option_list($months, strftime('%m', strtotime($_SESSION['user_array']['birthday'])));

    echo "<form method=\"POST\" id=\"editForm\">" . $str . ": <select name=\"birthdate_month\">";

    foreach ($birthdate_month_options as $option) {

        echo $option;

    }

    echo "</select>";
    echo "<select name=\"birthdate_year\">";

    foreach ($birthdate_year_options as $option) {

        echo $option;

    }

    echo "</select>";
    echo "<input type=\"hidden\" name=\"edit\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"user\" value=\"" . $_SESSION['user_array']['user'] . "\">" . PHP_EOL;
    echo "<input type=\"checkbox\" name=\"h" . $edit . "\" value=\"true\"";

    if (ord($_SESSION['user_array']['h' . $edit])) {

        echo " checked=\"checked\" ";

    }

    echo '/> ' . PR_HIDE;
    echo "[<a href=\"#\" onclick=\"javascript:document.getElementById('editForm').submit();return false;\">" . PR_SEND
        . "</a>]<br /></form>";

    return false;

}

/**
 * @param string $str
 * @param string $edit
 * @param \PDO $link
 * @return bool|null
 */
function change_selector($str, $edit, $link)
{

    if (change($edit, $link)) {

        return true;

    }

    switch ($edit) {

        case "input_language":
            $options = get_interface_languages_list($link);
            break;

        case "interface_language":
            $options = get_interface_languages_list($link);
            break;

        case "studies_type":
            $options = eval(STUDIES_TYPES_LIST);
            break;

        case "studies_level":
            $options = eval(STUDIES_LEVELS_LIST);
            break;

        default:
            return null;

    }

    $edit_options = option_list($options, $_SESSION['user_array'][$edit]);

    echo "<form method=\"POST\" id=\"editForm\">" . $str . ": <select name=\"$edit\">";

    foreach ($edit_options as $option) {

        echo $option;

    }

    echo "</select>";

    echo "<input type=\"hidden\" name=\"edit\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"user\" value=\"" . $_SESSION['user_array']['user'] . "\">" . PHP_EOL;
    echo "<input type=\"checkbox\" name=\"h" . $edit . "\" value=\"true\"";

    if (ord($_SESSION['user_array']['h' . $edit])) {
        echo " checked=\"checked\" ";
    }

    echo '/> ' . PR_HIDE;
    echo "[<a href=\"#\" onclick=\"javascript:document.getElementById('editForm').submit();return false;\">" . PR_SEND
        . "</a>]<br /></form>";

    return false;

}

/**
 * @param string $edit
 * @param PDO $link
 * @return bool
 */
function change_email($edit, $link)
{

    $error = 0;

    if (isset($_POST[$edit])) {

        $_POST[$edit] = trim($_POST[$edit]);

    }

    $change = (isset($_POST['edit'])) ? ((($_POST['edit'] == $edit)
        && (($error = parse_email($_POST[$edit])) === 0)) ? true : false) : false;

    if ($change) {

        if ($_POST[$edit] === $_SESSION['user_array']['email']) {

            change($edit, $link);
            return true;

        }

    }

    if ($change) {

        if ($_POST[$edit] === "") {

            change($edit, $link);
            $_SESSION['user_array']['confirmed_email'] = chr(0);
            return true;

        }

        if (captcha_verify_word()) {

            $sql = "SELECT user, email FROM confirmed_emails where email = :email";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':edit', $_POST[$edit], PDO::PARAM_STR);

            if ($stmt->execute() !== false && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if ($row['user'] !== $_SESSION['user_array']['user']) {

                    $error = 3;

                }

            }

            if (!$error) {

                if (change($edit, $link)) {

                    send_confirmation_email($_POST[$edit], $link);
                    $_SESSION['user_array']['email'] = $_POST[$edit];

                    echo PR_EMAIL . ': ' . $_SESSION['user_array']['email'] . " ";
                    echo (ord($_SESSION['user_array']['hemail']) ? PR_HIDDEN : PR_VISIBLE)
                        . " [<a href=\"profile.phtml?user=" . $_SESSION['user_array']['user'] . "&edit=email\">"
                        . PR_EDIT . "</a>] <span style=color:red>" . PR_SENT_1_A
                        . "</span><script type='text/javascript'>alert(\"" . PR_SENT_1 . "\\n" . PR_SENT_2 . "\")</script><br />"
                        . PHP_EOL;

                    return false;

                }

            }

        } else {

            $error = 4;

        }

    }

    echo "<form id=\"editForm\" method=\"POST\">" . PR_EMAIL . ": <input type=\"text\" id=\"" . $edit . "\" name=\""
        . $edit . "\" value=\"" . $_SESSION['user_array'][$edit]
        . "\" onfocus=\"javascript:document.getElementById('focusId').value='" . $edit . "';\" />";
    echo "<input type=\"hidden\" id=\"focusId\" name=\"focusId\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"edit\" value=\"" . $edit . "\">" . PHP_EOL;
    echo "<input type=\"hidden\" name=\"user\" value=\"" . $_SESSION['user_array']['user'] . "\">" . PHP_EOL;
    echo "<input type=\"checkbox\" name=\"h" . $edit . "\" value=\"true\"";

    if (ord($_SESSION['user_array']['h' . $edit])) {
        echo " checked=\"checked\" ";
    }

    echo '/>' . PR_HIDE;

    switch ($error) {

        case 1:
            echo "<span style=\"color:red\"> " . PR_ERR_MAIL_LONG . "</span><script type='text/javascript'>focusId='"
                . $edit . "'</script>";
            break;

        case 2:
            echo "<span style=\"color:red\"> " . PR_ERR_MAIL_INVALID . "</span><script type='text/javascript'>focusId='"
                . $edit . "'</script>";
            break;

        case 3:
            echo "<span style=\"color:red\"> " . PR_ERR_MAIL_ALREADY_ASSOC . "</span><script type='text/javascript'>focusId='"
                . $edit . "';document.getElementById('" . $edit . "').value='" . $_POST[$edit] . "';</script>";
            break;

    }

    echo "<br /><span id=\"captchaImage\" style=\"border:0;width:140px;\"><img src=\"." . SYNAPP_CAPTCHA_PATH . "/captcha.image.php?nocache="
        . hash("sha256", time() . mt_rand()) . "\" alt=\"captcha\"/></span><a 
href=\"#\" onclick=\"updateCaptcha(null, '." . SYNAPP_CAPTCHA_PATH ."' );return false;\"><img src=\".". SYNAPP_UI_RESOURCES_PATH ."/images/refresh.png\" style=\"border:0\" alt=\""
        . PR_REFRESH . "\" title=\"" . PR_REFRESH . "\"/></a>";
    echo "<br />" . PR_CAPT . "<input type=\"text\" id=\"magicword\" "
        . "onfocus=\"javascript:document.getElementById('focusId').value='magicword';\" name=\"magicword\" autocomplete=\"off\" />";

    if ($error == 4) {
        echo "<span style=\"color:red\"> " . PR_ERR_CAPT . "</span><script type='text/javascript'>"
            . "focusId='magicword';document.getElementById('" . $edit . "').value='" . $_POST[$edit] . "';</script>";
    }

    echo '<br /></form>' . PHP_EOL;

    if (isset($_GET['alert']) && !count($_POST)) {

        if ($_GET['alert'] === "true") {

            echo "<script type='text/javascript'>alert(\"" . PR_VALIDATE_MAIL . "\")</script>";

        }

    }

    return false;

}

/**
 * @param string $edit
 * @param string $user
 * @param PDO $link
 * @return bool
 */
function change_avatar($edit, $user, $link)
{

    $avatar = "";

    if (count($_POST)) {

        if ($_POST['edit'] == $edit) {

            $avatar = process_avatar($user);

            if (($avatar !== "error1") && ($avatar !== "error2")) {

                $editCol = "`".str_replace("`","``",$edit)."`";
                $sql = "UPDATE users SET {$editCol} = :avatar WHERE user=:user";
                $stmt = $link->prepare($sql);
                $stmt->bindValue(':avatar', $avatar, PDO::PARAM_STR);
                $stmt->bindValue(':user', $user, PDO::PARAM_STR);
                if ($stmt->execute() === false) {

                    die ("Error: " . var_export($link->errorInfo(), true));

                } else {

                    ob_end_clean();
                    header('Location: profile.phtml?user=' . $user);

                    exit;

                }

            }

        }

    }

    echo "<form method=\"POST\" enctype=\"multipart/form-data\" id=\"editForm\">"
        . PR_AVATAR
        . ": <input type=\"file\" name=\""
        . $edit . "\" />";
    echo "<a href=\"#\" onclick=\"javascript:document.getElementById('editForm').submit();return false;\">"
        . PR_SEND . "</a>";
    echo "<input type=\"hidden\" name=\"edit\" value=\""
        . $edit . "\">"
        . PHP_EOL;
    echo "<input type=\"hidden\" name=\"user\" value=\""
        . $_SESSION['user_array']['user'] . "\">"
        . PHP_EOL;

    if ($avatar === "error1") {

        echo "<span style=\"color:red\"> "
            . PR_AVATAR_UPLOAD_ERROR . "</span>";

    } elseif ($avatar === "error2") {

        echo "<span style=\"color:red\"> "
            . PR_AVATAR_UPLOAD_MAX_SIZE_EXCEEDED . "</span>";

    }

    echo '<BR /></form>' . PHP_EOL;

    return false;

}