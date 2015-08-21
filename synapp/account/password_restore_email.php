<?php
/**
 * @param string $input
 */
function password_restore_email($input)
{
    $link = connect();
    if (parse_email($input, 0) == 0) {
        $sql = "SELECT * FROM users WHERE email = :input AND confirmed_email = 1";
        $isemail = true;
    } else {
        $sql = "SELECT * FROM users WHERE user = :input";
        $isemail = false;
    }
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':input', $input, PDO::PARAM_STR);
    $stmt->execute();
    $link = null;
    $ua = $stmt->fetch(PDO::FETCH_ASSOC);
    $emailnotfound = true;
    $usernotfound = true;
    if (isset($ua['email']) && $ua['email'] !== '') {
        $usernotfound = false;
        if ($ua['email'] != '' && parse_email($ua['email']) == 0 && $ua['confirmed_email'] == 1) {
            $emailnotfound = false;
        }
    }
    if (!$emailnotfound) {
        $user = $ua['user'];
        $code = mt_rand(); //recovery
        $link = connect();
        $sql = "UPDATE users SET recovery = :recovery WHERE user = :user";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':recovery', hash("sha256", $code), PDO::PARAM_STR);
        $stmt->bindValue(':user', $user);
        $stmt->execute();
        $email = $ua['email'];
        $to = $email;
        $subject = passresout('subject', $user);
        $mime_boundary = hash("sha256", time());
        $msg = passresout('email', $user, $email, $code, $mime_boundary);
        $headers = passresout('headers', $mime_boundary);
        mail($to, $subject, $msg, $headers);
        if ($isemail) {
            echo passresout('prmailsenttoaddress', $input);
        } else {
            echo passresout('prmailsenttouser', $input);
        }
    } else {
        if ($isemail) {
            echo passresout('emailnotfound', $input);
        } else {
            if ($usernotfound) {
                echo passresout('usernotfound', $input);
            } else {
                echo passresout('novalidemailassociated', $input);
            }
        }
    }
}
