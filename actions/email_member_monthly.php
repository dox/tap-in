<?php
include_once("../inc/autoload.php");

// get start/end of last month
$start = date('Y-m-d', strtotime("first day of -1 month"));
$end = date('Y-m-d', strtotime("last day of -1 month"));

$sql = "SELECT * FROM staff WHERE email IS NOT NULL AND email != '' ORDER BY lastname ASC, firstname ASC";
$staffAll = $db->get($sql);

foreach ($staffAll as $staff) {
	$staff = new Staff($staff['uid']);
	$shifts = $staff->shiftsBetweenDates($start, $end);
	
	$output  = "<h1>" . $staff->fullname() . "</h1>";
	$output .= "<p>You have logged " . count($shifts) . " shifts, totalling " . convertMinutesToHours($staff->totalMinutesBetweenDates($start, $end)) . "</p>";
	
	$output .= "<ul>";
	foreach ($shifts AS $shift) {
		$shift = new Shift($shift['uid']);
		
		$output .= "<li>" . dateDisplay($shift->shift_start) . " - " .  convertMinutesToHours($shift->totalMinutes()) . "</li>";
	}
	
	$output .= "</ul>";
	
	//$output .= "Emailing to: " . $staff->email;
	
	echo $output;
}


?>