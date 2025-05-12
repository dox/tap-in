<div class="container">
	<div class="row align-items-start">
		<div class="col-md-8">
			<h2>Total Worked Hours</h2>
			<div id="chart">
			</div>
		</div>
		<div class="col-md-4">
			<?php
			$sql = "SELECT uid FROM shifts WHERE shift_end IS NULL ORDER BY shift_start DESC";
			$openShifts = $db->get($sql);
			
			if (count($openShifts) > 0) {
				echo "<h2>Currently Open Shifts</h2>";
				
				$output  = "<ol class=\"list-group list-group mb-3\">";
				foreach ($openShifts AS $shift) {
					$shift = new Shift($shift['uid']);
					$output .= $shift->listGroupItem();
				}
				$output .= "</ol>";
				
				echo $output;
			}
			
			$limit = setting('staff_previous_shifts_display');
			$sql = "SELECT uid FROM shifts WHERE shift_end IS NOT NULL ORDER BY shift_start DESC LIMIT " . $limit;
			$recentShifts = $db->get($sql);
			
			echo "<h2>Recent Shifts</h2>";
			
			$output  = "<ol class=\"list-group list-group mb-3\">";
			foreach ($recentShifts AS $shift) {
				$shift = new Shift($shift['uid']);
				$output .= $shift->listGroupItem();
			}
			$output .= "</ol>";
			
			echo $output;
			?>
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