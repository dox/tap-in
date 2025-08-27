<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$uid = $_POST['uid'] ?? null;

	if (isset($_POST['delete']) && $uid) {
		// Handle delete request
		$dbAttempt = $db->delete('staff', 'uid', $uid);
		
		if ($dbAttempt) {
			echo alert('success', "Deleted!", "Staff record deleted successfully.");
		} else {
			echo alert('danger', "Error!", "Failed to delete staff record.");
		}
	} else {
		// Handle create/update
		$data = [
			'firstname' => $_POST['firstname'],
			'lastname'  => $_POST['lastname'],
			'code'      => $_POST['code'],
			'category'  => $_POST['category'],
			'email'     => $_POST['email'],
			'payroll_id'=> $_POST['payroll_id'],
			'enabled'   => isset($_POST['enabled']) ? 1 : 0,
		];

		if ($uid) {
			$dbAttempt = $db->update('staff', $data, 'uid', $uid);
		} else {
			$dbAttempt = $db->create('staff', $data);
		}

		if ($dbAttempt) {
			$logData = [
				'category' => 'staff',
				'result'   => 'success',
				'description' => 'Staff record for ' . $uid . ' updated with ' . implode(", ", $_POST)
			];
			$log->create($logData);
			
			echo alert('success', "Success!", "Staff updated successfully!");
		} else {
			$logData = [
				'category' => 'staff',
				'result'   => 'warning',
				'description' => 'Staff record for ' . $uid . ' failed to update with ' . implode(", ", $_POST)
			];
			$log->create($logData);
			
			echo alert('danger', "Error!", "Failed to update staff.");
		}
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
$table  = "<table id=\"staffTable\" class=\"table\">";
$table .= "<thead>";
$table .= "<tr>";
$table .= "<th scope=\"col\" onclick=\"sortTable(0)\">Name</th>";
$table .= "<th scope=\"col\" onclick=\"sortTable(1)\">Code</th>";
$table .= "<th scope=\"col\" onclick=\"sortTable(2)\">Last Tap-In</th>";
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


<script>
function sortTable(colIndex) {
  const table = document.getElementById("staffTable");
  const rows = Array.from(table.rows).slice(1); // skip header
  const isAsc = table.getAttribute("data-sort-dir") !== "asc"; // toggle asc/desc
  
  rows.sort((a, b) => {
	let x = a.cells[colIndex].innerText.toLowerCase();
	let y = b.cells[colIndex].innerText.toLowerCase();

	// try numeric comparison if both are numbers
	if (!isNaN(x) && !isNaN(y)) {
	  x = Number(x);
	  y = Number(y);
	}
	return isAsc ? (x > y ? 1 : -1) : (x > y ? -1 : 1);
  });

  // put sorted rows back into tbody
  const tbody = table.tBodies[0];
  rows.forEach(row => tbody.appendChild(row));

  // save new sort direction
  table.setAttribute("data-sort-dir", isAsc ? "asc" : "desc");
}
</script>