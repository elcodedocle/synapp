<?php
/**
 * @return int
 */
function notify_no_mail()
{
    exec('sudo mail -ef /var/mail/root', $output, $return_var);
    if ($return_var == '0') {
        /* There's already new mail. Do not send notification. */
        return 0;
    }


    /* There is no new mail but this function has been called, so there is going to be -> Send notification */

    $email = "gael.abadin@gmail.com";
    $subject = "New message from " . $_SESSION['user_array']['user'] . " at SynAPP";
    $mime_boundary = hash("sha256", time());
    $user = isset($_SESSION['user_array']) ? $_SESSION['user_array']['user'] : "anonymous";
    $msg = "You have a new message from user " . $user . " at SynAPP.";
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
    $headers .= "Content-Type: text/plain; content=\"utf-8\"" . PHP_EOL;
    mail($email, $subject, $msg, $headers);

    return 1;
}
