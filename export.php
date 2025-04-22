<?php
include_once("inc/autoload.php");
requireLogin(); // Redirects if not logged in

if ($user->isLoggedIn()) {
	$requestedPage = isset($_GET['page']) ? $_GET['page'] : 'index';
} else {
	die("Not logged in");
}

$pagePath = __DIR__ . "/pages/export_{$requestedPage}.php";

$fileName = $requestedPage;
if (!empty(($_POST['date_range']))) {
	list($from, $to) = explode('|', $_POST['date_range']);
	$fileName = $fileName . "_" . $from . "_to_" . $to;
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $fileName . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

include_once($pagePath);

// Close output stream
fclose($output);

$log->create([
	'category'    => 'report',
	'result'      => 'success',
	'description' => 'Report generated: ' . $fileName
]);
exit;
?>