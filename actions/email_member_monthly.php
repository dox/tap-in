<?php
include_once("../inc/autoload.php");

// get start/end of last month
$start = date('Y-m-d', strtotime("first day of this month"));
$end = date('Y-m-d', strtotime("last day of this month"));

$sql = "SELECT * FROM staff WHERE email IS NOT NULL AND email != '' ORDER BY lastname ASC, firstname ASC";
$staffAll = $db->get($sql);

foreach ($staffAll as $staff) {
	$staff = new Staff($staff['uid']);
	$shifts = $staff->shiftsBetweenDates($start, $end);
	$totalMinutesRoundedUp = 0;
	
	foreach ($shifts AS $shift) {
		$shift = new Shift($shift['uid']);
		
		$totalMinutesRoundedUp += $shift->totalMinutesRoundedUp();
	}
	
	if ($totalMinutesRoundedUp > 0) {
		
		$output  = "<h1>" . $staff->fullname() . "</h1>";
		$output .= "<p>Dear " . $staff->firstname . ",</p>";
		$output .= "<p>Please find below a summary of the shifts you have worked at St Edmund Hall during the period from " . $start . " to " . $end . ":</p>";
		
		$output .= "<p>Total Shifts: " . count($shifts) . "<br/>";
		$output .= "Total Hours Worked: " . convertMinutesToHours($totalMinutesRoundedUp) . "</p>";
		$output .= "<p>Summary:</p>";
		$output .= "<ul>";
		foreach ($shifts AS $shift) {
			$shift = new Shift($shift['uid']);
			
			$output .= "<li>" . dateDisplay($shift->shift_start) . " - " .  convertMinutesToHours($shift->totalMinutesRoundedUp()) . "</li>";
		}
		
		$output .= "</ul>";
		
		$output .= "<p>If you have any questions about the information above or notice anything that seems incorrect, please don’t hesitate to get in touch.</p>";
		
		$output .= "<p>Thank you for your continued support and hard work — it's very much appreciated.</p>";
		
		$output .= "<p>Best regards,</p>";
		
		
		
		//$output .= "Emailing to: " . $staff->email;
		echo $output;
	}
	
}


?>