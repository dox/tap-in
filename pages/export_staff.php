<?php
// Define column keys (in desired order)
$columns = [
	'staff_uid',
	'staff_name',
	'staff_code',
	'total_minutes',
	'total_hours'
];

// Output header row
fputcsv($output, $columns);

// Get data
$params = [];
$where = '';

if (!empty($_GET['status'])) {
	$status = $_GET['status'];

	if (!empty($_GET['status']) && $_GET['status'] === 'enabled') {
		$where = "WHERE enabled = :enabled";
		$params['enabled'] = '1';
	}
}

$sql = "SELECT * FROM staff $where ORDER BY lastname ASC, firstname ASC";

// Now use parameter binding — assuming $db->query($sql, $params)
$staffAll = $db->query($sql, $params);

// Output data rows
foreach ($staffAll as $staff) {
	$staff = new Staff($staff['uid']);
	
	$row = [];

	// Assign each value in order
	$row[] = $staff->uid;
	$row[] = $staff->fullname();
	$row[] = $staff->code;
	$row[] = $staff->totalMinutes();
	$row[] = convertMinutesToHours($staff->totalMinutes());

	fputcsv($output, $row);
}
?>
