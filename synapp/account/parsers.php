<?php

require_once dirname(__FILE__) . '/config/deployment_environment.php';
require_once dirname(
        __FILE__
    ) . '/' . SYNAPP_CONFIG_DIRNAME . '/profile_constants_constraints_defaults_and_selector_values.php';
/**
 * @param string $str
 * @param string $minlength
 * @param string $maxlength
 * @param int $baseerrcode
 * @return int
 */
function parse($str, $minlength = USER_MINLENGTH, $maxlength = USER_MAXLENGTH, $baseerrcode = 0)
{

    $validchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_.";

    if ($str == '') {
        return 1 + $baseerrcode;
    }

    $length = strlen($str);

    if ($length < $minlength) {
        return 2 + $baseerrcode;
    }

    if ($length > $maxlength) {
        return 3 + $baseerrcode;
    }

    if ((strpos($str, "__") !== false) || (strpos($str, "..") !== false) || (strpos($str, "_.") !== false) || (strpos(
                $str,
                "._"
            ) !== false)
    ) {
        return 4 + $baseerrcode;
    }

    if ((strpos($str, ".") === 0) || (strpos($str, "_") === 0) || (strpos($str, ".") === ($length - 1)) || (strpos(
                $str,
                "_"
            ) === ($length - 1))
    ) {
        return 4 + $baseerrcode;
    }

    for ($i = 0; $i < $length; $i++) {
        if (strpbrk($str[$i], $validchars) === false) {
            return 4 + $baseerrcode;
        }
    }

    return 0;

}

/**
 * @param string $email
 * @param int $baseerrcode
 * @return int
 */
function parse_email($email, $baseerrcode = 0)
{
    if ($email == '') {
        return 0;
    }
    $longitud = strlen($email);
    if ($longitud > EMAIL_MAXLENGTH) {
        return 1 + $baseerrcode;
    }
    /*if (preg_match("^[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$", $email))
    return 0; else return 2+$baseerrcode; */
    if (strpos($email, "--") !== false) {
        return 2 + $baseerrcode;
    }
    $temp = explode('@', $email);
    if (count($temp) != 2) {
        return 2 + $baseerrcode;
    }
    if ((strpos($temp[0], "-") === 0)) {
        return 2 + $baseerrcode;
    }
    if ((strpos($temp[0], "-") === strlen($temp[0]) - 1)) {
        return 2 + $baseerrcode;
    }
    if ((strpos($temp[1], "-") === 0)) {
        return 2 + $baseerrcode;
    }
    if ((strpos($temp[1], "-") === strlen($temp[1]) - 1)) {
        return 2 + $baseerrcode;
    }
    $temp3 = explode('-', $temp[0]);
    $j = count($temp3);
    for ($i = 0; $i < $j; $i++) {
        if (($mailusererr = parse($temp3[$i], 1, 128)) != 0) {
            die ("Error: " . $mailusererr);
        } //;return 2+$baseerrcode;
    }
    $temp2 = explode('.', $temp[1]);
    if (count($temp2) < 2) {
        return 2 + $baseerrcode;
    }
    $temp3 = explode('-', $temp[1]);
    $j = count($temp3);
    for ($i = 0; $i < $j; $i++) {
        if (($mailusererr = parse($temp3[$i], 1, 128)) != 0) {
            return 2 + $baseerrcode;
        }
    }
    if (strpos($temp[1], "_")) {
        return 2 + $baseerrcode;
    }
    return 0;
}
