<?php
// Initialize the session.
session_start();

// Unset all of the session variables.
$_SESSION = array();

// Delete the session cookie to destroy the session, and not just the session data.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session.
session_destroy();
if (isset($_GET['close'])) {
    echo "<!DOCTYPE html><html><head><meta charset=\"utf-8\"><script type=\"text/javascript\">function sendandclose()" .
        "{window.opener.location='index.php';window.close();}</script>" .
        "<title>logout</title></head><body onload=\"sendandclose();\"></body></html>";
} else {
    header("Location: " . (isset($_GET['location']) ? $_GET['location'] : '../admlogin.phtml'));
    die();
}
exit;
