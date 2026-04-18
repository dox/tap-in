<?php
// Define column keys (in desired order)
$columns = [
	'shift_uid',
	'staff_uid',
	'staff_name',
	'staff_code',
	'staff_payroll_id',
	'shift_start',
	'shift_end',
	'shift_total_minutes',
	'shift_total_hours',
	'shift_total_minutes_rounded_up',
	'shift_total_hours_rounded_up'
];

// Output header row
fputcsv($output, $columns);

// Get data
$params = [];
$where = '';
$dateRange = parsePostedDateRange($_POST['date_range'] ?? null);

if ($dateRange) {
	$where = "WHERE DATE(shift_start) BETWEEN :from AND :to";
	$params['from'] = $dateRange['from'];
	$params['to'] = $dateRange['to'];
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
	$row[] = $staff->payroll_id;
	$row[] = dateDisplay($shift->shift_start, true);
	$row[] = dateDisplay($shift->shift_end, true);
	$row[] = $shift->totalMinutes();
	$row[] = convertMinutesToHours($shift->totalMinutes());
	$row[] = $shift->totalMinutesRoundedUp();
	$row[] = convertMinutesToHours($shift->totalMinutesRoundedUp());

	fputcsv($output, $row);
}
?>
