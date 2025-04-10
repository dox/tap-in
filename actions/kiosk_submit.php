<?php
include_once("../inc/autoload.php");

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userNumber = $input['userNumber'] ?? null;

$response = [
	'success' => false,
	'message' => 'No code provided'
];

// Early return if no user number
if (!$userNumber) {
	$log->create([
		'category'    => 'login',
		'result'      => 'warning',
		'description' => $response['message']
	]);
	echo json_encode($response);
	exit;
}

$staff = new Staff(['code' => $userNumber]);

// Early return if no matching staff member
if (empty($staff->uid)) {
	$response = [
		'success' => false,
		'message' => "Unknown code: " . htmlspecialchars($userNumber)
	];
	
	$log->create([
		'category'    => 'login',
		'result'      => 'warning',
		'description' => $response['message']
	]);
	echo json_encode($response);
	exit;
}

// matched!  Let's create/update shift
if ($staff->openShift()) {
	// already an open shift - let's close it
	$shiftData = [
		'shift_end' => date('c')
	];
	$success = $db->update('shifts', $shiftData, 'staff_uid', $staff->uid);
	
	$response = [
		'success' => true,
		'message' => "Shift ended for " . htmlspecialchars($userNumber) . " " . $staff->fullname()
	];
	$result = 'success';
} else {
	// new shift
	$shiftData = [
		'staff_uid'    => $staff->uid,
		'shift_start'  => date('c')
	];
	$success = $db->create('shifts', $shiftData);
	
	$response = [
		'success' => true,
		'message' => "Shift started for " . htmlspecialchars($userNumber) . " " . $staff->fullname()
	];
	$result = 'success';
	
}
$log->create([
	'category'    => 'login',
	'result'      => $result,
	'description' => $response['message']
]);

echo json_encode($response);