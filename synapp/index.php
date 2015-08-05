<?php session_start();
$location = isset($_SESSION['adm']) && $_SESSION['adm'] === true ? 'admin/admusers.phtml': (isset($_SESSION['auth']) ? ($_SESSION['auth'] ? "watrix.phtml" : "login.phtml") : "login.phtml");
header('Location: ' . $location);

exit;
