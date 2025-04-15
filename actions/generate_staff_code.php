<?php
include_once("../inc/autoload.php");

// Generate a list of existing codes
$sql = "SELECT code FROM staff";
$staffAll = $db->get($sql);
$existingCodes = array_column($staffAll, 'code');

// Length of new codes
$codeLength = (int) setting('kiosk_code_length');
$maxAttempts = 100; // Prevent infinite loops

$newCode = null;

for ($i = 0; $i < $maxAttempts; $i++) {
	$suggested = str_pad(rand(0, pow(10, $codeLength) - 1), $codeLength, '0', STR_PAD_LEFT);
	if (!in_array($suggested, $existingCodes)) {
		$newCode = $suggested;
		break;
	}
}

if ($newCode) {
	echo json_encode(['code' => $newCode]);
} else {
	$logData = [
		'category' => 'staff',
		'result'   => 'warning',
		'description' => 'Unable to generate new code for staff'
	];
	$log->create($logData);
	
	http_response_code(500);
}