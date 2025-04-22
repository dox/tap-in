<?php
// Define column keys (in desired order)
$columns = [
	'staff_uid',
	'staff_name',
	'staff_code',
	'staff_payroll_id',
	'total_minutes',
	'total_hours'
];

// Output header row
fputcsv($output, $columns);

// Get staff
$sql = "SELECT * FROM staff ORDER BY lastname ASC, firstname ASC";
$staffAll = $db->query($sql);

// Output data rows
foreach ($staffAll as $staff) {
	$staff = new Staff($staff['uid']);
	$totalMinutes = 0;
	
	// Get shifts
	if (!empty(($_POST['date_range']))) {
		list($from, $to) = explode('|', $_POST['date_range']);
		
		// Basic date format check (YYYY-MM-DD)
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
			$totalMinutes = $staff->totalMinutesBetweenDates($from, $to);
		}
	}
	
	if ($totalMinutes > 0) {
		$row = [];
		
		// Assign each value in order
		$row[] = $staff->uid;
		$row[] = $staff->fullname();
		$row[] = $staff->code;
		$row[] = $staff->payroll_id;
		$row[] = $totalMinutes;
		$row[] = convertMinutesToHours($totalMinutes);
		
		fputcsv($output, $row);
	}
	
}
?>
