<?php
session_start();
require_once dirname(__FILE__) . '/../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../connect.php';
require_once dirname(__FILE__) . '/../languages/get_browser_language.php';
require_once dirname(__FILE__) . '/..' . SYNAPP_CAPTCHA_PATH . '/captcha.php';

$error = 0;
if (!isset($_SESSION['if_lang'])) {
    $lang = getDefaultLanguage();
    if (stripos($lang, "es") === 0) {
        $_SESSION['if_lang'] = "spa";
    } elseif (stripos($lang, "pl") === 0) {
        $_SESSION['if_lang'] = "pol";
    } else {
        $_SESSION['if_lang'] = "eng";
    }
}
$langfile = 'profiletxt.php';
if (file_exists(dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/' . $langfile)) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(__FILE__) . '/../languages/' . $_SESSION['if_lang'] . '/' . $langfile;
} else {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(__FILE__) . '/../languages/eng/' . $langfile;
}
if ((isset($_POST['user'])) && (isset($_POST['code']))) {
    $user = $_POST['user'];
    $code = $_POST['code'];
    if (captcha_verify_word()) {
        $link = connect();
        $sql = "SELECT * FROM users WHERE user = :user";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':user', $user, PDO::PARAM_STR);
        if ($stmt->execute()===false||$stmt->rowCount()<1||($row = $stmt->execute())===false) {
            die ("Invalid request.");
        }
        $_SESSION['if_lang'] = $row['interface_language'];
        if ($row['email_confirmation_code'] === hash('sha256', $code)) {
            $break = false;
            $insert = true;
            $sql = "SELECT * FROM confirmed_emails WHERE email = :email";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':email', $row['email'], PDO::PARAM_STR);
            if ($stmt->execute()!==false && ($confirmed_email = $stmt->fetch(PDO::PARAM_STR))!==false) {
                if ($confirmed_email['user'] !== $_POST['user']) {
                    $body = PR_ERR_MAIL_ALREADY_ASSOC; /* If the user matches the error would be the $code mismatch */
                    $break = true;
                } else {
                    $insert = false;
                }
            }
            if (!$break) {
                if ($insert) {
                    $sql = "INSERT INTO confirmed_emails VALUES ( :user , :email )";
                    $stmt = $link->prepare($sql);
                    $stmt->bindValue(':user', $_POST['user'], PDO::PARAM_STR);
                    $stmt->bindValue(':email', $row['email'], PDO::PARAM_STR);
                    $stmt->execute();
                }
                $newccode = hash('sha256', mt_rand());
                $sql = "UPDATE users SET email_confirmation_code = :newccode , confirmed_email = b'1' WHERE user = :user";
                $stmt = $link->prepare($sql);
                $stmt->bindValue(':newccode', $newccode, PDO::PARAM_STR);
                $stmt->bindValue(':user', $user, PDO::PARAM_STR);
                $stmt->execute();
                if (isset($_SESSION['user_array'])) {
                    if ($_SESSION['user_array']['user'] === $user) {
                        $_SESSION['user_array']['confirmed_email'] = chr(1);
                        $_SESSION['user_array']['email_confirmation_code'] = $newccode;
                    }
                }
                $body = "<p>" . PR_CONFIRM_SUCCESS_1 . "</p>";
                $body .= "<p><a href='../index.php'>" . PR_CONFIRM_SUCCESS_2 . "</a></p>";
            }
        } else {
            $body = PR_CONFIRM_FAIL;
        }
    } else {
        $error = 1;
    }
} else {
    if ((!isset($_GET['user'])) || (!isset($_GET['code']))) {
        die ("Invalid request.");
    } else {
        $user = $_GET['user'];
        $code = $_GET['code'];
    }
}

if (!isset($body)) {
    $body = "<h1>" . PR_CONFIRM_HEADER . "</h1>
<form enctype=\"multipart/form-data\" method=\"post\" id=\"confirmationForm\">
<input type='hidden' name=\"user\" value=\"" . $user . "\">
<input type='hidden' name=\"code\" value=\"" . $code . "\">
<span id=\"captchaImage\" style=\"width:140px;\"><img src=\"..".SYNAPP_CAPTCHA_PATH."/captcha.image.php?nocache=" . hash(
            'sha256',
            time() . mt_rand()
        ) . "\" border=\"0\" /></span><a 
href=\"#\" onclick=\"updateCaptcha('" . dirname(
            dirname($_SERVER['PHP_SELF'])
        ) . "/');return false;\"><img src=\"..".SYNAPP_UI_RESOURCES_PATH."/images/refresh.png\" border=\"0\" alt=\"" . PR_REFRESH . "\" title=\"" . PR_REFRESH . "\"/></a>
<br />" . PR_CAPT . "<input name=\"magicword\" type=\"text\" /> ";
    if ($error === 1) {
        $body .= "<span style=\"color:red\"> " . PR_ERR_CAPT . "</span>";
    }
    $body .= "<br /><input type='submit' value=\"" . PR_CONFIRM_SEND . "\" />
</form>";
}

echo "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><title>" . PR_CONFIRM_TITLE . "</title>
<script type='text/javascript' src=\"..".SYNAPP_CAPTCHA_PATH."/js/updateCaptcha.js\" ></script>
<script type='text/javascript'>
function stopRKey(evt) {
    var node;
    evt = (evt) ? evt : ((event) ? event : null);
    node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    if ((evt.keyCode == 13) && (node.type==\"text\"))  {document.getElementById('confirmationForm').submit();}
}
document.onkeypress = stopRKey;
</script> 
</head>
<body>
" . $body . "
</body>
</html>";
