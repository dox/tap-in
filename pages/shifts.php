<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Gather the POST data into an array
	$data = [
		'staff_uid' => $_POST['staff_uid'],
		'shift_start' => $_POST['shift_start'],
		'shift_end' => $_POST['shift_end'],
	];
	
	// Update the shift record in the database
	$updateSuccess = $db->update('shifts', $data, 'uid', $_POST['uid']);

	if ($updateSuccess) {
		echo alert('success', "Success!", "Shift updated successfully!");
	} else {
		echo alert('danger', "Error!", "Failed to update shift.");
	}
}

$sql = "SELECT * FROM shifts ORDER BY shift_start DESC";
$shiftsAll = $db->get($sql);
?>

<h1>Shifts</h1>
<?php
$table  = "<table class=\"table\">";
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

foreach ($shiftsAll as $shift) {
	$shift = new Shift($shift['uid']);
	$table .= $shift->tableRow();
}

$table .= "</tbody>";
$table .= "</table>";

echo $table;

?>