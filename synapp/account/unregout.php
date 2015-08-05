<?php
/**
 * @param string $arg1
 * @param string $arg2
 * @param string $arg3
 * @param int $arg4
 * @param string $arg5
 * @return null|string
 */
function unregout($arg1, $arg2 = "", $arg3 = "", $arg4 = 0, $arg5 = "")
{

    switch ($arg1) {
        case ($arg1 === "subject"):
            return UR_MAIL_SUBJECT_1 . $arg2 . UR_MAIL_SUBJECT_2;
            break;
        case ($arg1 === "headers"):
            # Common Headers
            $time = time();
            $now = (int)(date('Y', $time) . date('m', $time) . date('j', $time));
            $mime_boundary = $arg2;
            $headers = 'From: WATRIX mailer <noreply@watrix.com>' . PHP_EOL;
            $headers .= 'Reply-To: noreply <noreply@watrix.com>' . PHP_EOL;
            $headers .= 'Return-Path: noreply <noreply@watrix.com>' . PHP_EOL; // these two to set reply address
            $headers .= "Message-ID:<" . $now . " admin@" . $_SERVER['SERVER_NAME'] . ">" . PHP_EOL;
            $headers .= "X-Mailer: PHP v" . phpversion() . PHP_EOL; // These two to help avoid spam-filters
            # Boundry for marking the split & Multitype Headers
            $headers .= 'MIME-Version: 1.0' . PHP_EOL;
            $headers .= "Content-Type: text/plain; boundary=\"" . $mime_boundary . "\"" . PHP_EOL;
            return $headers;
        case ($arg1 === "email"):
            $mime_boundary = $arg5;
            $msg = "";

            # Text Version
            $msg .= UR_MAIL_TO . " " . $arg3;
            $msg .= " " . UR_MAIL_USR_1 . " " . $arg2;
            $msg .= UR_MAIL_USR_2 . PHP_EOL . PHP_EOL;
            $msg .= UR_MAIL_NR . PHP_EOL . PHP_EOL;
            $msg .= UR_MAIL_IGNORE . PHP_EOL . PHP_EOL;
            $msg .= UR_MAIL_DO . PHP_EOL . PHP_EOL;
            $msg .= "http://www.whatisthewatrix.com/unregister.phtml?user=" . $arg2 . "&code=" . $arg4;
            /*
            # HTML Version
            $msg .= "--".$mime_boundary.PHP_EOL;
            $msg .= "Content-Type: text/html; charset=iso-8859-1".PHP_EOL;
            $msg .= "Content-Transfer-Encoding: 8bit".PHP_EOL;
            $msg="<HTML><HEAD><TITLE>" . UR_MAIL_TITLE . "</TITLE></HEAD><BODY>";
            $msg.=UR_MAIL_TO . " " . $arg3;
            $msg .=" " . UR_MAIL_USR_1 . " " . $arg2;
            $msg .=UR_MAIL_USR_2 . "<BR /><BR />";
            $msg.=UR_MAIL_NR . "<BR /><BR />";
            $msg.=UR_MAIL_IGNORE . "<BR /><BR />";
            $msg.=UR_MAIL_DO . "<BR /><BR />";
            $msg.="<A HREF=\"http://www.watrix.com/passres.phtml?user=" . $arg2 . "&code=" . $arg4 . "\">http://www.whatisthewatrix.com/unregister.phtml?user=" . $arg2 . "&code=" . $arg4 . "</A>";
            $msg.="</BODY></HTML>";
            */
            $msg .= PHP_EOL . PHP_EOL;

            # Finished
            $msg .= "--" . $mime_boundary . "--" . PHP_EOL . PHP_EOL; // finish with two eol's for better security. see Injection. 
            return $msg;
            break;
        case ($arg1 === "urmailsenttouser"):
            $out = "<HTML><HEAD><TITLE>" . UR_SENT_TITLE_2 . " " . $arg2 . "</TITLE></HEAD>" . PHP_EOL;
            $out .= "<BODY><P>" . UR_SENT_2 . " " . $arg2 . "." . PHP_EOL;
            $out .= "<BR>" . UR_SENT_DO . "</P>" . PHP_EOL;
            $out .= '<BR><p><a href="index.php">' . UR_GOTO_HOME . "</a></p></BODY>" . PHP_EOL;
            $out .= "</HTML>" . PHP_EOL;
            return $out;
            break;
        default:
            return null;
    }
}
