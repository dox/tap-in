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
		New Staff Member
	<?php endif; ?>
</h1>

<div class="container">
	<?php if ($uid): ?>
	<div class="row">
		<!-- Stats cards -->
		<?php foreach ([['Total Hours', $totalHours], ['Last Month (' . date('F', strtotime('first day of last month')) . ')', $totalLastMonth], ['This Month (' . date('F') . ')', $totalThisMonth], ['This Week', $totalThisWeek]] as [$label, $value]): ?>
			<div class="col">
				<div class="card mb-3">
					<div class="card-body">
						<div class="subheader text-nowrap text-truncate"><?= $label ?></div>
						<div class="h1 text-truncate"><?= convertMinutesToHours($value) ?></div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
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
				
				<input type="hidden" name="uid" value="<?= htmlspecialchars($staff->uid ?? '') ?>" />
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>

		<?php if ($uid): ?>
		<div class="col-md-4">
			<div id="chart"></div>
			Recent Shifts
			<ol class="list-group">
				<?php
				foreach ($staff->recentShifts() as $shiftData) {
					$shift = new Shift($shiftData['uid']);
					$url = "index.php?page=shift_edit&uid=" . $shift->uid;
					$badgeClass = empty($shift->shift_end) ? "text-bg-primary" : "text-bg-success";
					$hours = convertMinutesToHours($shift->totalMinutes());
					$icon = is_null($shift->shift_end) ? " " . icon('hourglass-split') : "";
					
					$output  = "<li class=\"list-group-item d-flex justify-content-between align-items-start\">";
					$output .= "<div class=\"ms-2 me-auto\">";
					$output .= "<div class=\"fw-bold\">" . $staff->fullname() . "</div>";
					$output .= dateDisplay($shift->shift_start, true);
					$output .= "</div>";
					$output .= $icon . "<a href=\"" . $url . "\"><span class=\"badge " . $badgeClass . " . rounded-pill\">" . $hours . "</span></a>";
					$output .= "</li>";
					
					echo $output;
				}
				?>
			</ol>
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
</script>
