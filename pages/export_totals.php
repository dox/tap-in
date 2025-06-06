<?php
// Define column keys (in desired order)
$columns = [
	'staff_uid',
	'total_month',
	'staff_name',
	'staff_code',
	'staff_payroll_id',
	'total_shifts',
	'total_minutes',
	'total_hours',
	'total_minutes_rounded_up',
	'total_hours_rounded_up'
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
	
	$totalMinutes = 0;
	$totalMinutesRounded = 0;
	
	// Get shifts
	if (!empty(($_POST['date_range']))) {
		list($from, $to) = explode('|', $_POST['date_range']);
		
		// Basic date format check (YYYY-MM-DD)
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
			$shifts = $staff->shiftsBetweenDates($from, $to);
		}
		
		foreach ($shifts AS $shift) {
			$shift = new Shift($shift['uid']);
			
			$totalMinutes += $shift->totalMinutes();
			$totalMinutesRounded += $shift->totalMinutesRoundedUp();
		}
	}
	
	if ($totalMinutes > 0) {
		$row = [];
		
		// Assign each value in order
		$row[] = $staff->uid;
		$row[] = date('Y F', strtotime($from));
		$row[] = $staff->fullname();
		$row[] = $staff->code;
		$row[] = $staff->payroll_id;
		$row[] = count($shifts);
		$row[] = $totalMinutes;
		$row[] = convertMinutesToHours($totalMinutes);
		$row[] = $totalMinutesRounded;
		$row[] = convertMinutesToHours($totalMinutesRounded);
		
		fputcsv($output, $row);
	}
	
}
?>
