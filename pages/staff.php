<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$uid = $_POST['uid'] ?? null;
	$data = [
		'firstname' => $_POST['firstname'],
		'lastname'  => $_POST['lastname'],
		'code'      => $_POST['code'],
		'category'  => $_POST['category'],
		'email'     => $_POST['email'],
		'enabled'   => isset($_POST['enabled']) ? 1 : 0,
	];

	if ($uid) {
		$dbAttempt = $db->update('staff', $data, 'uid', $uid);
	} else {
		$dbAttempt = $db->create('staff', $data);
	}
	
	if ($dbAttempt) {
		echo alert('success', "Success!", "Staff updated successfully!");
	} else {
		echo alert('danger', "Error!", "Failed to update staff.");
	}
}



$sql = "SELECT * FROM staff ORDER BY enabled DESC, lastname ASC, firstname ASC";
$staffAll = $db->get($sql);

?>

<h1><?php echo icon('person', '1em'); ?> Staff</h1>
<div class="pb-3 text-end">
	<a class="btn btn-success" href="index.php?page=staff_edit" role="button"><?php echo icon('person-add'); ?> Add New</a>
</div>

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