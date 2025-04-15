<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Gather the POST data into an array
	$data = [
		'staff_uid' => $_POST['staff_uid'],
		'shift_start' => $_POST['shift_start'],
		'shift_end' => $_POST['shift_end'],
	];
	
	if (empty($_POST['shift_end'])) {
		$data['shift_end'] = null;
	}
	
	// Update the shift record in the database
	$updateSuccess = $db->update('shifts', $data, 'uid', $_POST['uid']);

	if ($updateSuccess) {
		echo alert('success', "Success!", "Shift updated successfully!");
	} else {
		echo alert('danger', "Error!", "Failed to update shift.");
	}
}

$sql = "SELECT * FROM shifts ORDER BY uid DESC";
$shiftsAll = $db->get($sql);
?>

<h1><?php echo icon('hourglass-split', '1em'); ?> Shifts</h1>
<?php
// Set reference dates in 'Y-m-d' format
$todayStr = (new DateTime('today'))->format('Y-m-d');
$yesterdayStr = (new DateTime('yesterday'))->format('Y-m-d');
$mondayThisWeekStr = (new DateTime('monday this week'))->format('Y-m-d');
$mondayLastWeekStr = (new DateTime('monday this week'))->modify('-7 days')->format('Y-m-d');

// Initialise groups
$grouped = [
	'Today' => [],
	'Yesterday' => [],
	'This Week' => [],
	'Last Week' => [],
	'Older' => [],
];

foreach ($shiftsAll as $shiftData) {
	$shift = new Shift($shiftData['uid']);
	
	// Extract just the date part of shift_start
	$shiftDateStr = (new DateTime($shift->shift_start))->format('Y-m-d');

	if ($shiftDateStr === $todayStr) {
		$grouped['Today'][] = $shift;
	} elseif ($shiftDateStr === $yesterdayStr) {
		$grouped['Yesterday'][] = $shift;
	} elseif ($shiftDateStr >= $mondayThisWeekStr) {
		$grouped['This Week'][] = $shift;
	} elseif ($shiftDateStr >= $mondayLastWeekStr && $shiftDateStr < $mondayThisWeekStr) {
		$grouped['Last Week'][] = $shift;
	} else {
		$grouped['Older'][] = $shift;
	}
}

// Output the grouped shifts
foreach ($grouped as $label => $shiftsGroup) {
	if (empty($shiftsGroup)) continue;

	$table  = "<h2>{$label}</h2>";
	
	$table .= "<table class=\"table\">";
	$table .= "<thead>";
	$table .= "<tr>";
	$table .= "<th scope=\"col\">Name</th>";
	$table .= "<th scope=\"col\">Shift Start</th>";
	$table .= "<th scope=\"col\">Shift End</th>";
	$table .= "<th scope=\"col\">Duration</th>";
	$table .= "<th scope=\"col\"></th>";
	$table .= "</tr>";
	$table .= "</thead>";
	$table .= "<tbody>";
	
	foreach ($shiftsGroup as $shift) {
		//echo "<li>{$shift->shift_start} — {$shift->staff_uid}</li>\n";
		$table .= $shift->tableRow();
	}
	//echo "</ul>\n";
	
	$table .= "</tbody>";
	$table .= "</table>";
	
	echo $table;
}
?>