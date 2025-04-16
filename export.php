<?php
include_once("inc/autoload.php");
requireLogin(); // Redirects if not logged in

if ($user->isLoggedIn()) {
	$requestedPage = isset($_GET['page']) ? $_GET['page'] : 'index';
} else {
	die("Not logged in");
}

$pagePath = __DIR__ . "/pages/export_{$requestedPage}.php";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export.csv"');

// Open output stream
$output = fopen('php://output', 'w');

include_once($pagePath);

// Close output stream
fclose($output);
exit;
?>