<?php
include_once("inc/autoload.php");
requireLogin(); // Redirects if not logged in

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit('Method not allowed');
}

if (!csrfTokenIsValid($_POST['csrf_token'] ?? null)) {
	http_response_code(403);
	exit('Invalid CSRF token');
}

if ($user->isLoggedIn()) {
	$requestedPage = isset($_GET['page']) ? $_GET['page'] : 'index';
} else {
	die("Not logged in");
}

$pagePath = __DIR__ . "/pages/export_{$requestedPage}.php";
$dateRange = parsePostedDateRange($_POST['date_range'] ?? null);

$fileName = $requestedPage;
if ($dateRange) {
	$fileName = $fileName . "_" . $dateRange['from'] . "_to_" . $dateRange['to'];
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
