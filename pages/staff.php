<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Gather the POST data into an array
	$data = [
		'firstname' => $_POST['firstname'],
		'lastname' => $_POST['lastname'],
		'code' => $_POST['code'],
	];
	
	// Update the shift record in the database
	$updateSuccess = $db->update('staff', $data, 'uid', $_POST['uid']);

	if ($updateSuccess) {
		echo alert('success', "Success!", "Staff updated successfully!");
	} else {
		echo alert('danger', "Error!", "Failed to update staff.");
	}
}

$sql = "SELECT * FROM staff ORDER BY lastname ASC";
$staffAll = $db->get($sql);

?>

<h1>Staff</h1>
<?php
$table  = "<table class=\"table\">";
$table .= "<thead>";
$table .= "<tr>";
$table .= "<th scope=\"col\">Name</th>";
$table .= "<th scope=\"col\">Code</th>";
$table .= "<th scope=\"col\">Last Tap-In</th>";
$table .= "</tr>";
$table .= "</thead>";
$table .= "<tbody>";

foreach ($staffAll as $staff) {
	$staff = new Staff($staff['uid']);
	$table .= $staff->tableRow();
}

$table .= "</tbody>";
$table .= "</table>";

echo $table;

?>