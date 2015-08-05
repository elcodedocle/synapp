<?php
// routing script for php builtin development web server
$path = pathinfo($_SERVER["SCRIPT_FILENAME"]);
if ($path["extension"] == "phtml") {
    header('Content-Type: text/html; charset=utf-8');
    /** @noinspection PhpIncludeInspection */
    include $_SERVER["SCRIPT_FILENAME"];
} else {
    return false;
}
