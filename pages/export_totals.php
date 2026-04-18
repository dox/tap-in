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
$dateRange = parsePostedDateRange($_POST['date_range'] ?? null);
$from = $dateRange['from'] ?? null;
$to = $dateRange['to'] ?? null;
$periodLabel = $from ? date('Y F', strtotime($from)) : '';

// Output data rows
foreach ($staffAll as $staff) {
	$staff = new Staff($staff['uid']);
	$totalMinutes = 0;
	$totalMinutesRounded = 0;
	$shifts = [];
	
	if ($dateRange) {
		$shifts = $staff->shiftsBetweenDates($from, $to);
		
		foreach ($shifts as $shift) {
			$shift = new Shift($shift['uid']);
			
			$totalMinutes += $shift->totalMinutes();
			$totalMinutesRounded += $shift->totalMinutesRoundedUp();
		}
	}
	
	if ($totalMinutes > 0) {
		$row = [];
		
		// Assign each value in order
		$row[] = $staff->uid;
		$row[] = $periodLabel;
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
