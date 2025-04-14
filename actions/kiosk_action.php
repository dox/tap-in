<?php
include_once("../inc/autoload.php");

$userId = $_POST['userId'] ?? '';
$action = $_POST['action'] ?? '';

if (!$userId || !$action || !in_array($action, ['start', 'end'])) {
	echo '<p>Invalid request.</p>';
	exit;
}

if ($action === 'start') {
	$shiftData = [
		'staff_uid'    => $userId,
		'shift_start'  => date('c')
	];
	$success = $db->create('shifts', $shiftData);
	echo '<h1><span class="badge bg-success">Shift Started</span></h1>';
} else {
	$shiftData = [
		'shift_end' => date('c')
	];
	$success = $db->update('shifts', $shiftData, 'staff_uid', $userId);
	echo '<h1><span class="badge bg-success">Shift Ended</span></h1>';
}