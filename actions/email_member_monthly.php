<?php
include_once("../inc/autoload.php");

// get start/end of last month
$start = date('Y-m-d', strtotime('monday last week'));
$end = date('Y-m-d', strtotime('sunday last week'));

$sql = "SELECT * FROM staff WHERE email IS NOT NULL AND email != '' ORDER BY lastname ASC, firstname ASC";
$staffAll = $db->get($sql);
$sendFrom = setting('email_sender_address');

foreach ($staffAll as $staff) {
	$staff = new Staff($staff['uid']);
	$shifts = $staff->shiftsBetweenDates($start, $end);
	$totalMinutesRoundedUp = 0;
	
	foreach ($shifts AS $shift) {
		$shift = new Shift($shift['uid']);
		
		$totalMinutesRoundedUp += $shift->totalMinutesRoundedUp();
	}
	
	if ($totalMinutesRoundedUp > 0) {
		
		//$message  = "<h1>" . $staff->fullname() . "</h1>";
		$message  = "<p>Dear " . $staff->firstname . ",</p>";
		$message .= "<p>Please find below a summary of the shifts you have worked at St Edmund Hall between the " . date('dS \of F', strtotime($start)) . " to the " . date('dS \of F', strtotime($end)) . ":</p>";
		
		$message .= "<p>Total Shifts: " . count($shifts) . "<br/>";
		$message .= "Total Hours Worked: " . convertMinutesToHours($totalMinutesRoundedUp) . "</p>";
		$message .= "<p>Summary:</p>";
		$message .= "<ul>";
		
		foreach ($shifts AS $shift) {
			$shift = new Shift($shift['uid']);
			
			$message .= "<li>" . dateDisplay($shift->shift_start) . " - " .  convertMinutesToHours($shift->totalMinutesRoundedUp()) . "</li>";
		}
		
		$message .= "</ul>";
		
		$message .= "<p>If you have any questions about the information above or notice anything that seems incorrect, please don’t hesitate to get in touch.</p>";
		
		$message .= "<p>Best regards,</p>";
		
		$to = $staff->email;
		$subject = 'Your shifts summary';
		// Set content-type header for HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
		
		// Additional headers
		$headers .= 'From: ' . $sendFrom . "\r\n";
		$headers .= 'Reply-To: ' . $sendFrom . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
		
		$log->create([
			'category'    => 'email',
			'result'      => 'success',
			'description' => 'Email sent to: ' . $staff->email . '. Message: ' . $message
		]);
	}
}
?>