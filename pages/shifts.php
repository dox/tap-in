<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$uid = $_POST['uid'] ?? null;

	if (isset($_POST['delete']) && $uid) {
		// Handle delete request
		$dbAttempt = $db->delete('shifts', 'uid', $uid);
		
		if ($dbAttempt) {
			echo alert('success', "Deleted!", "Shift deleted successfully.");
		} else {
			echo alert('danger', "Error!", "Failed to delete shift.");
		}
	} else {
		// Handle create/update
		$data = [
			'staff_uid' => $_POST['staff_uid'],
			'shift_start' => $_POST['shift_start'],
			'shift_end' => $_POST['shift_end']
		];
		
		if (empty($_POST['shift_end'])) {
			$data['shift_end'] = null;
		}
		
		if ($uid) {
			$dbAttempt = $db->update('shifts', $data, 'uid', $uid);
		} else {
			$dbAttempt = $db->create('shifts', $data);
		}

		if ($dbAttempt) {
			$logData = [
				'category' => 'shift',
				'result'   => 'success',
				'description' => 'Shift record for ' . $uid . ' updated with ' . implode(", ", $_POST)
			];
			$log->create($logData);
			
			echo alert('success', "Success!", "Shift updated successfully!");
		} else {
			$logData = [
				'category' => 'shift',
				'result'   => 'warning',
				'description' => 'Shift record for ' . $uid . ' failed to update with ' . implode(", ", $_POST)
			];
			$log->create($logData);
			
			echo alert('danger', "Error!", "Failed to update shift.");
		}
	}
}

function parseShiftFilterDate($value) {
	if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
		return null;
	}

	$date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
	$errors = DateTimeImmutable::getLastErrors();

	if ($date === false) {
		return null;
	}

	if (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
		return null;
	}

	return $date;
}

$today = new DateTimeImmutable('today');
$defaultTo = $today;
$defaultFrom = $today->modify('-29 days');

$fromInput = $_GET['from'] ?? $defaultFrom->format('Y-m-d');
$toInput = $_GET['to'] ?? $defaultTo->format('Y-m-d');

$fromDate = parseShiftFilterDate($fromInput);
$toDate = parseShiftFilterDate($toInput);

if (!$fromDate || !$toDate) {
	echo alert('warning', 'Invalid date range', 'Showing the last 30 days instead.');
	$fromDate = $defaultFrom;
	$toDate = $defaultTo;
}

if ($fromDate > $toDate) {
	[$fromDate, $toDate] = [$toDate, $fromDate];
}

$fromValue = $fromDate->format('Y-m-d');
$toValue = $toDate->format('Y-m-d');

$sql = "SELECT * FROM shifts WHERE DATE(shift_start) BETWEEN :from AND :to ORDER BY shift_start DESC, uid DESC";
$shiftsAll = $db->query($sql, [
	'from' => $fromValue,
	'to' => $toValue,
]);
?>

<h1><?php echo icon('hourglass-split', '1em'); ?> Shifts</h1>
<div class="row g-3 align-items-end pb-3">
	<div class="col-12 col-lg">
		<form action="index.php" method="get" class="row g-3 align-items-end">
			<input type="hidden" name="page" value="shifts">
			<div class="col-12 col-md-4">
				<label for="from" class="form-label">From</label>
				<input type="date" class="form-control" id="from" name="from" value="<?php echo htmlspecialchars($fromValue); ?>" max="<?php echo htmlspecialchars($toValue); ?>">
			</div>
			<div class="col-12 col-md-4">
				<label for="to" class="form-label">To</label>
				<input type="date" class="form-control" id="to" name="to" value="<?php echo htmlspecialchars($toValue); ?>" min="<?php echo htmlspecialchars($fromValue); ?>">
			</div>
			<div class="col-12 col-md-auto">
				<button class="btn btn-outline-secondary w-100" type="submit">Apply</button>
			</div>
		</form>
	</div>
	<div class="col-12 col-lg-auto text-end">
		<a class="btn btn-success" href="index.php?page=shift_edit" role="button"><?php echo icon('hourglass-split'); ?> Add New</a>
	</div>
</div>

<?php
echo '<p class="text-muted">Showing ' . count($shiftsAll) . ' shifts from ' . htmlspecialchars($fromDate->format('j M Y')) . ' to ' . htmlspecialchars($toDate->format('j M Y')) . '.</p>';

if (empty($shiftsAll)) {
	echo alert('info', 'No shifts found', 'There are no shifts in the selected date range.');
} else {
	$table  = "<table class=\"table mb-5\">";
	$table .= "<thead>";
	$table .= "<tr>";
	$table .= "<th scope=\"col\">Name</th>";
	$table .= "<th scope=\"col\" style=\"width:20%\">Shift Start</th>";
	$table .= "<th scope=\"col\" style=\"width:20%\">Shift End</th>";
	$table .= "<th scope=\"col\" style=\"width:10%\">Duration</th>";
	$table .= "<th scope=\"col\" style=\"width:10%\"></th>";
	$table .= "</tr>";
	$table .= "</thead>";
	$table .= "<tbody>";

	foreach ($shiftsAll as $shiftData) {
		$shift = new Shift($shiftData['uid']);
		$table .= $shift->tableRow();
	}

	$table .= "</tbody>";
	$table .= "</table>";

	echo $table;
}
?>
