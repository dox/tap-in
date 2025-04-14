<?php
include_once("../inc/autoload.php");

$userNumber = $_POST['userNumber'] ?? '';

// Early return if no user number
if (!$userNumber) {
	$log->create([
		'category'    => 'login',
		'result'      => 'warning',
		'description' => 'No tap-in code provided'
	]);
	echo '<h1>Invalid Code</h1>';
	echo '<p>No tap-in code provided</p>';
	exit;
}

$staff = new Staff(['code' => $userNumber]);

// Early return if no matching staff member
if (empty($staff->uid)) {
	$log->create([
		'category'    => 'login',
		'result'      => 'warning',
		'description' => 'No staff member found with number ' . $userNumber
	]);
	echo '<h1>Invalid Code</h1>';
	echo '<p>No staff member found with number ' . $userNumber . '</p>';
	exit;
}

echo '<h1>Welcome, ' . htmlspecialchars($staff->firstname) . '</h1>';

if ($staff->openShift()) {
	echo '<p>You have an open shift.</p>';
	echo '<button class="btn btn-danger btn-lg" onclick="performShiftAction(' . $staff->uid . ', \'end\')">' . icon('stop-fill', '1.5em') . ' End Shift</button>';
} else {
	echo '<p>You are not currently clocked in.</p>';
	echo '<button class="btn btn-success btn-lg" onclick="performShiftAction(' . $staff->uid . ', \'start\')">' . icon('play-fill', '1.5em') . ' Start Shift</button>';
}