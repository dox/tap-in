<?php
$uid = $_GET['uid'] ?? null;
$staff = $uid ? new Staff($uid) : new Staff(); // assumes Staff() can init blank

// Show stats only for existing staff
if ($uid) {
	$totalHours = $staff->totalMinutesBetweenDates();
	$totalLastMonth = $staff->totalMinutesBetweenDates(date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month')));
	$totalThisMonth = $staff->totalMinutesBetweenDates(date('Y-m-01'), date('Y-m-t'));
	$totalThisWeek = $staff->totalMinutesBetweenDates(date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday this week')));
}
?>

<h1>
	<?php if ($uid): ?>
		<kbd><?= $staff->code ?></kbd> <?= $staff->fullname(); ?>
	<?php else: ?>
		Create New Staff Member
	<?php endif; ?>
</h1>

<div class="container">
	<?php if ($uid): ?>
	<div class="row">
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">Total Hours</div>
					<div class="h1 text-truncate"><?= convertMinutesToHours($totalHours) ?></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">Last Month (<?= date('F', strtotime('first day of last month')); ?>)</div>
					<div class="h1 text-truncate"><?= convertMinutesToHours($totalLastMonth) ?></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">This Month (<?= date('F', strtotime('first day of this month')); ?>)</div>
					<div class="h1 text-truncate"><?= convertMinutesToHours($totalThisMonth) ?></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">This Week</div>
					<div class="h1 text-truncate"><?= convertMinutesToHours($totalThisWeek) ?></div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="row">
		<div class="col-md-8">
			<form class="needs-validation" method="POST" action="index.php?page=staff" novalidate>
				<div class="mb-3">
					<label for="firstname" class="form-label">First Name</label>
					<input type="text" class="form-control" id="firstname" name="firstname" value="<?= htmlspecialchars($staff->firstname ?? '') ?>">
				</div>
				<div class="mb-3">
					<label for="lastname" class="form-label">Last Name</label>
					<input type="text" class="form-control" id="lastname" name="lastname" value="<?= htmlspecialchars($staff->lastname ?? '') ?>">
				</div>
				<div class="mb-3">
					<label for="code" class="form-label">Tap-In Code</label>
					<div class="input-group">
						<input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($staff->code ?? '') ?>" <?= $uid ? 'readonly' : '' ?>>
						<button type="button" class="btn btn-outline-secondary" id="generateCodeBtn"><?= icon('arrow-repeat') ?></button>
					</div>
				</div>
				<div class="mb-3">
					<label for="category" class="form-label">Category</label>
					<select id="category" name="category" class="form-select">
						<?php
						$categories = explode(",", setting('staff_categories'));
						foreach ($categories as $category) {
							$selected = ($category == ($staff->category ?? '')) ? " selected" : "";
							echo "<option value='" . htmlspecialchars($category) . "'$selected>" . htmlspecialchars($category) . "</option>";
						}
						?>
					</select>
					<div id="categoryHelp" class="form-text">Add more <a href="index.php?page=settings&settingUID=20">categories here</a></div>
				</div>
				<div class="mb-3">
					<label for="email" class="form-label">Email Address</label>
					<input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($staff->email ?? '') ?>">
				</div>
				<div class="mb-3">
					<label for="payroll_id" class="form-label">Payroll ID</label>
					<input type="text" class="form-control" id="payroll_id" name="payroll_id" value="<?= htmlspecialchars($staff->payroll_id ?? '') ?>">
				</div>
				<div class="mb-3">
					<input class="form-check-input" type="checkbox" value="1" id="enabled" name="enabled" <?= ($staff->enabled ?? '') == "1" ? 'checked' : '' ?>>
					<label class="form-check-label" for="enabled">Enabled</label>
				</div>
				
				<button type="submit" class="btn btn-primary">Submit</button>
				<button type="submit" name="delete" value="1" class="btn btn-danger" onclick="return confirmDelete();">Delete</button>
				<input type="hidden" name="uid" value="<?= htmlspecialchars($staff->uid ?? '') ?>" />
			</form>
		</div>

		<?php if ($uid): ?>
		<div class="col-md-4">
			<div id="chart"></div>
			<?php
			if (count($staff->openShifts()) > 0) {
				echo "<h2>Currently Open Shifts</h2>";
				
				$output  = "<ol class=\"list-group list-group mb-3\">";
				foreach ($staff->openShifts() AS $shift) {
					$shift = new Shift($shift['uid']);
					$output .= $shift->listGroupItem();
				}
				$output .= "</ol>";
				
				echo $output;
			}
			?>
			
			<?php
			echo "<h2>Recent Shifts</h2>";
			
			$output  = "<ol class=\"list-group list-group mb-3\">";
			foreach ($staff->recentShifts() AS $shift) {
				$shift = new Shift($shift['uid']);
				$output .= $shift->listGroupItem();
			}
			$output .= "</ol>";
			
			echo $output;
			?>
		</div>
		<?php endif; ?>
	</div>
</div>

<script>
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
	form.addEventListener('submit', event => {
	  if (!form.checkValidity()) {
		event.preventDefault()
		event.stopPropagation()
	  }
	  form.classList.add('was-validated')
	}, false)
  })
})()
</script>


<?php
// Array to hold the last 30 days
$lastXDays = [];
$xDays = 10;
// Loop through the last x days days
for ($i = 0; $i < $xDays; $i++) {
	// Subtract $i days from today and format the date
	$date = date('Y-m-d', strtotime("-$i days"));
	
	$sql = "SELECT SUM(
		TIMESTAMPDIFF(MINUTE, shift_start, IFNULL(shift_end, NOW()))
	) AS total_minutes
	FROM shifts
	WHERE staff_uid = '" . $staff->uid . "' AND DATE(shift_start) = '" . $date . "'";
	
	$shiftsSum = $db->get($sql);
	
	if ($shiftsSum[0]['total_minutes'] > 0) {
		$total = ($shiftsSum[0]['total_minutes'] / 60);
	} else {
		$total = 0;
	}
	
	$lastXDays["'" . $date . "'"] = $total;
}

ksort($lastXDays);
?>
<script>
const rawDates = [<?php echo implode(", ", array_keys($lastXDays)); ?>];

const formattedDates = rawDates.map(dateStr => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-GB', { month: 'short', day: '2-digit' });
  // Output: "08 Apr", "09 Apr", etc.
});

var options = {
  chart: {
	type: 'bar'
  },
  series: [{
	name: 'Total Hours Worked',
	data: [<?php echo implode(", ", $lastXDays); ?>]
  }],
  xaxis: {
	categories: formattedDates
  },
  yaxis: {
	labels: {
	  formatter: function (val) {
		return val.toFixed(1);
	  }
	}
  },
  dataLabels: {
	formatter: function (val) {
	  return val.toFixed(1);
	}
  }
}

var chart = new ApexCharts(document.querySelector("#chart"), options);

chart.render();

function confirmDelete() {
	confirm("You are not permitted to delete members of staff.  Please contact the IT Office to perform this action.");
	
	return false;
}
</script>
