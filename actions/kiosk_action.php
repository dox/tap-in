<?php
include_once("../inc/autoload.php");

$userId = $_POST['userId'] ?? '';
$action = $_POST['action'] ?? '';

if (!$userId || !$action || !in_array($action, ['start', 'end'])) {
	echo '<p>Invalid request.</p>';
	exit;
}

$staff = new Staff($userId);

if ($action === 'start') {
	$staff->tapin();
	echo '<h1><span class="badge bg-success">Shift Started</span></h1>';
} else {
	$staff->tapout();
	echo '<h1><span class="badge bg-success">Shift Ended</span></h1>';
}