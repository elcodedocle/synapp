<?php
/**
 * @param string $email
 * @param \PDO $link
 */
function send_confirmation_email($email, $link)
{
    $user = $_SESSION['user_array']['user'];
    $code = mt_rand(); //validation code
    
    $sql = "UPDATE users SET email_confirmation_code = :confirmationcode, confirmed_email = b'0' WHERE user = :user";
    
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':confirmationcode', hash("sha256",$code), PDO::PARAM_STR);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    $stmt->execute();
    
    $subject = PR_MAIL_SUBJECT_1 . $user . PR_MAIL_SUBJECT_2;
    $mime_boundary = hash("sha256", time());
    $msg = PR_MAIL_TO . " " . $email;
    $msg .= " " . PR_MAIL_USR_1 . " " . $user;
    $msg .= PR_MAIL_USR_2 . PHP_EOL . PHP_EOL;
    $msg .= PR_MAIL_NR . PHP_EOL . PHP_EOL;
    $msg .= PR_MAIL_IGNORE . PHP_EOL . PHP_EOL;
    $msg .= PR_MAIL_DO . PHP_EOL . PHP_EOL;
    $msg .= SYNAPP_BASE_URL_HTTP . "/account/confirm_email.php?user=" . $user . "&code=" . $code . " " . PR_MAIL_DO_HTTP . PHP_EOL . PHP_EOL;
    $msg .= SYNAPP_BASE_URL_HTTPS . "/account/confirm_email.php?user=" . $user . "&code=" . $code . " " . PR_MAIL_DO_HTTPS;
    $msg .= PHP_EOL . PHP_EOL;
    $msg .= "--" . $mime_boundary . "--" . PHP_EOL . PHP_EOL; // finish with two eol's for better security. see Injection. 
    # Common Headers
    $time = time();
    $now = (int)(date('Y', $time) . date('m', $time) . date('j', $time));
    $headers = 'From: SYNAPP mailer <noreply@SYNAPP.com>' . PHP_EOL;
    $headers .= 'Reply-To: noreply <noreply@SYNAPP.com>' . PHP_EOL;
    $headers .= 'Return-Path: noreply <noreply@SYNAPP.com>' . PHP_EOL; // these two to set reply address
    $headers .= "Message-ID:<" . $now . " admin@" . $_SERVER['SERVER_NAME'] . ">" . PHP_EOL;
    $headers .= "X-Mailer: PHP v" . phpversion() . PHP_EOL; // These two to help avoid spam-filters
    # Boundry for marking the split & Multitype Headers
    $headers .= 'MIME-Version: 1.0' . PHP_EOL;
    $headers .= "Content-Type: text/plain; charset=\"utf-8\"" . PHP_EOL . PHP_EOL;
    mail($email, $subject, $msg, $headers);
    $sql = "UPDATE users SET confirmed_email = 0 WHERE user = :user";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $user);
    $stmt->execute();
    $_SESSION['user_array']['confirmed_email'] = chr(0);
}
