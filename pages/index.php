<div class="container">
	<div class="row align-items-start">
		<div class="col-md-8">
			<h2>Total Worked Hours</h2>
			<div id="chart">
			</div>
		</div>
		<div class="col-md-4">
			<?php
			$sql = "SELECT * FROM shifts WHERE shift_end IS NULL ORDER BY shift_start DESC";
			$openShifts = $db->get($sql);
			
			if (count($openShifts) > 0) {
				echo "<h2>Currently Open Shifts</h2>";
				
				$output  = "<ol class=\"list-group list-group mb-3\">";
				foreach ($openShifts AS $shift) {
					$shift = new Shift($shift['uid']);
					$staff = new Staff($shift->staff_uid);
					$staffEditURL = "index.php?page=staff_edit&uid=" . $staff->uid;
					$shiftEditURL = "index.php?page=shift_edit&uid=" . $shift->uid;
					
					$icon = is_null($shift->shift_end) ? " " . icon('hourglass-split') : "";
					
					if (empty($shift->shift_end)) {
						$badgeClass = "text-bg-primary";
					} else {
						$badgeClass = "text-bg-success";
					}
					
					$output .= "<li class=\"list-group-item d-flex justify-content-between align-items-start\">";
					$output .= "<div class=\"ms-2 me-auto\">";
					$output .= "<div class=\"fw-bold\"><a href=\"" . $staffEditURL . "\">" . $staff->fullname() . "</a></div>";
					$output .= $shift->shift_start;
					$output .= "</div>";
					$output .= $icon . "<a href=\"" . $shiftEditURL . "\"><span class=\"badge " . $badgeClass . " rounded-pill\">" . convertMinutesToHours($shift->totalMinutes()) . "</a></span>";
					$output .= "</li>";
				}
				
				$output .= "</ol>";
				
				echo $output;
			}
			
			
			?>
			<h2>Recent Shifts</h2>
			
			<ol class="list-group list-group">
			<?php
			$limit = setting('staff_previous_shifts_display');
			
			$sql = "SELECT * FROM shifts WHERE shift_end IS NOT NULL ORDER BY shift_start DESC LIMIT " . $limit;
			$openShifts = $db->get($sql);
			
			foreach ($openShifts AS $shift) {
				$shift = new Shift($shift['uid']);
				$staff = new Staff($shift->staff_uid);
				$staffEditURL = "index.php?page=staff_edit&uid=" . $staff->uid;
				$shiftEditURL = "index.php?page=shift_edit&uid=" . $shift->uid;
				
				if (empty($shift->shift_end)) {
					$badgeClass = "text-bg-primary";
				} else {
					$badgeClass = "text-bg-success";
				}
				
				$output  = "<li class=\"list-group-item d-flex justify-content-between align-items-start\">";
				$output .= "<div class=\"ms-2 me-auto\">";
				$output .= "<div class=\"fw-bold\"><a href=\"" . $staffEditURL . "\">" . $staff->fullname() . "</a></div>";
				$output .= $shift->shift_start;
				$output .= "</div>";
				$output .= "<a href=\"" . $shiftEditURL . "\"><span class=\"badge " . $badgeClass . " rounded-pill\">" . convertMinutesToHours($shift->totalMinutes()) . "</a></span>";
				$output .= "</li>";
				
				echo $output;
			}
			?>
			</ol>
		</div>
	</div>
</div>




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
	WHERE DATE(shift_start) = '" . $date . "'";
	
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