<?php
/**
 * @param string $arg1
 * @param string $arg2
 * @param string $arg3
 * @param int $arg4
 * @param string $arg5
 * @return null|string
 */
function passresout($arg1, $arg2 = "", $arg3 = "", $arg4 = 0, $arg5 = "")
{

    switch ($arg1) {
        case ($arg1 === "subject"):
            return PR_MAIL_SUBJECT_1 . $arg2 . PR_MAIL_SUBJECT_2;
            break;
        case ($arg1 === "headers"):
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
            $headers .= "Content-Type: text/plain; charset=\"utf-8\"" . PHP_EOL;
            return $headers;
        case ($arg1 === "email"):
            $mime_boundary = $arg5;
            $msg = "";

            # Text Version
            $msg .= PR_MAIL_TO . " " . $arg3;
            $msg .= " " . PR_MAIL_USR_1 . " " . $arg2;
            $msg .= PR_MAIL_USR_2 . PHP_EOL . PHP_EOL;
            $msg .= PR_MAIL_NR . PHP_EOL . PHP_EOL;
            $msg .= PR_MAIL_IGNORE . PHP_EOL . PHP_EOL;
            $msg .= PR_MAIL_DO . PHP_EOL . PHP_EOL;
            $msg .= "http://synapp.info/account/passres.phtml?user=" . $arg2 . "&code=" . $arg4 . " " . PR_MAIL_DO_HTTP . PHP_EOL . PHP_EOL;
            $msg .= "https://synapp.info/account/passres.phtml?user=" . $arg2 . "&code=" . $arg4 . " " . PR_MAIL_DO_HTTPS;
            $msg .= PHP_EOL . PHP_EOL;

            # Finished
            $msg .= "--" . $mime_boundary . "--" . PHP_EOL . PHP_EOL; // finish with two eol's for better security. see Injection. 
            return $msg;
            break;
        case ($arg1 === "prmailsenttoaddress" || "prmailsenttouser" || "emailnotfound" || "usernotfound" || "novalidemailassociated"):
            $out = "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><title>" . PR_SENT_TITLE_1_B . " " . $arg2 . "</title></head>
            <body><p>" . PR_SENT_1_1_B . " " . $arg2 . " " . PR_SENT_1_2_B
                . "<br>" . PR_SENT_DO . "</p>
            <br><p><a href='index.php'>" . PR_GOTO_HOME . "</a></p></body>
            </html>" . PHP_EOL;
            return $out;
            break;
        /*case ($arg1===):
            $out = "<HTML><HEAD><TITLE>" . PR_SENT_TITLE_2 . " " . $arg2 . "</TITLE></HEAD>" . PHP_EOL;
            $out .= "<BODY><P>" . PR_SENT_2 . " " . $arg2 . "." . PHP_EOL;
            $out .= "<BR>" . PR_SENT_DO . "</P>" . PHP_EOL;
            $out .= '<BR><p><a href="index.php">' . PR_GOTO_HOME . "</a></p></BODY>" . PHP_EOL;
            $out .= "</HTML>" . PHP_EOL;
            return $out;
            break;
        case ($arg1===):
            $out = "<HTML><HEAD><TITLE>" . PR_UNSENT_TITLE_1 . " " . $arg2 . " " . PR_UNSENT_INSYNAPPDB . "</TITLE></HEAD>" . PHP_EOL;
            $out .= "<BODY><P>" . PR_UNSENT_NOMAIL . " " . $arg2 . " " . PR_UNSENT_INSYNAPPDB . PHP_EOL;
            $out .= "<br>" . PR_UNSENT_DO_1 . ' <a href="restore_account_request.phtml">' . PR_UNSENT_DO_2 . "</a> " . PR_UNSENT_DO_3 . ' <a href="register.phtml">' . PR_UNSENT_DO_4 . "</a>   " . PR_UNSENT_DO_5 . ' <a href="contact.php">' . PR_UNSENT_DO_6 . "</a> " . PR_UNSENT_DO_7 . " </P>" . PHP_EOL;
            $out .= '<p><a href="index.php">' . PR_GOTO_HOME . "</a></p></BODY>" . PHP_EOL;
            $out .= "</HTML>" . PHP_EOL;
            return $out;
            break;
        case ($arg1===):
            $out = "<HTML><HEAD><TITLE>" . PR_UNSENT_TITLE_2 . " " . $arg2 . " " . PR_UNSENT_INSYNAPPDB . "</TITLE></HEAD>" . PHP_EOL;
            $out .= "<BODY><P>" . PR_UNSENT_NOUSR . " " . $arg2 . " " . PR_UNSENT_INSYNAPPDB . PHP_EOL;
            $out .= "<br>" . PR_UNSENT_DO_1 . ' <a href="restore_account_request.phtml">' . PR_UNSENT_DO_2 . "</a> " . PR_UNSENT_DO_3 . ' <a href="register.phtml">' . PR_UNSENT_DO_4 . "</a>   " . PR_UNSENT_DO_5 . ' <a href="contact.php">' . PR_UNSENT_DO_6 . "</a> " . PR_UNSENT_DO_7 . " </P>" . PHP_EOL;
            $out .= '<p><a href="index.php">' . PR_GOTO_HOME . "</a></p></BODY>" . PHP_EOL;
            $out .= "</HTML>" . PHP_EOL;
            return $out;
            break;
        case ($arg1===):
            $out = "<HTML><HEAD><TITLE>" . PR_UNSENT_TITLE_3 . " " . $arg2 . " " . PR_UNSENT_INSYNAPPDB . "</TITLE></HEAD>" . PHP_EOL;
            $out .= "<BODY><P>" . PR_UNSENT_NOMAILTOUSR . " " . $arg2 . " " . PR_UNSENT_INSYNAPPDB . PHP_EOL;
            $out .= "<br>" . PR_UNSENT_DO_1 . ' <a href="restore_account_request.phtml">' . PR_UNSENT_DO_2 . "</a> " . PR_UNSENT_DO_3 . ' <a href="register.phtml">' . PR_UNSENT_DO_4 . "</a>   " . PR_UNSENT_DO_5 . ' <a href="contact.php">' . PR_UNSENT_DO_6 . "</a> " . PR_UNSENT_DO_7 . " </P>" . PHP_EOL;
            $out .= '<p><a href="index.php">' . PR_GOTO_HOME . "</a></p></BODY>" . PHP_EOL;
            $out .= "</HTML>" . PHP_EOL;
            return $out;
            break;*/
        default:
            return null;
    }
}
