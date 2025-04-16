<?php
// Define column keys (in desired order)
$columns = [
	'shift_uid',
	'staff_uid',
	'staff_name',
	'staff_code',
	'shift_start',
	'shift_end',
	'shift_duration_minutes',
	'shift_duration_hours'
];

// Output header row
fputcsv($output, $columns);

// Get data
$params = [];
$where = '';

if (!empty($_GET['from']) && !empty($_GET['to'])) {
	$from = $_GET['from'];
	$to = $_GET['to'];

	// Basic date format check (YYYY-MM-DD)
	if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
		$where = "WHERE shift_start >= :from AND shift_start <= :to";
		$params['from'] = $from;
		$params['to'] = $to;
	}
}

$sql = "SELECT * FROM shifts $where ORDER BY uid DESC";

// Now use parameter binding — assuming $db->query($sql, $params)
$shiftsAll = $db->query($sql, $params);

// Output data rows
foreach ($shiftsAll as $shift) {
	$shift = new Shift($shift['uid']);
	$staff = new Staff($shift->staff_uid);
	
	$row = [];

	// Assign each value in order
	$row[] = $shift->uid;
	$row[] = $staff->uid;
	$row[] = $staff->fullname();
	$row[] = $staff->code;
	$row[] = dateDisplay($shift->shift_start, true);
	$row[] = dateDisplay($shift->shift_end, true);
	$row[] = $shift->totalMinutes();
	$row[] = convertMinutesToHours($shift->totalMinutes());

	fputcsv($output, $row);
}
?>
