<?php
$staff = new Staff($_GET['uid']); // Assuming $logsClass is your Logs object

$totalHours = $staff->totalMinutesBetweenDates();
$totalLastMonth = $staff->totalMinutesBetweenDates(date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month')));
$totalThisMonth = $staff->totalMinutesBetweenDates(date('Y-m-01'), date('Y-m-t'));
$totalThisWeek = $staff->totalMinutesBetweenDates(date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday this week')));
?>

<h1><?php echo "<kbd>" . $staff->code . "</kbd> " . $staff->fullname(); ?></h1>


<div class="container">
	<div class="row">
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">Total Hours</div>
					<div class="h1 text-truncate"><?php echo convertMinutesToHours($totalHours); ?></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">Last Month (<?php echo date('F', strtotime('first day of last month'));?>)</div>
					<div class="h1 text-truncate"><?php echo convertMinutesToHours($totalLastMonth); ?></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">This Month (<?php echo date('F');?>)</div>
					<div class="h1 text-truncate"><?php echo convertMinutesToHours($totalThisMonth); ?></div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card mb-3">
				<div class="card-body">
					<div class="subheader text-nowrap text-truncate">This Week</div>
					<div class="h1 text-truncate"><?php echo convertMinutesToHours($totalThisWeek); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-8">
			<form class="needs-validation" method="POST" action="index.php?page=staff" novalidate>
				<div class="mb-3">
					<label for="firstname" class="form-label">First Name</label>
					<input type="input" class="form-control" id="firstname" name="firstname" value="<?php echo $staff->firstname; ?>">
				</div>
				<div class="mb-3">
					<label for="lastname" class="form-label">Last Name</label>
					<input type="input" class="form-control" id="lastname" name="lastname" value="<?php echo $staff->lastname; ?>">
				</div>
				<div class="mb-3">
					<label for="code" class="form-label">Tap-In Code</label>
					<input type="number" class="form-control" id="code" name="code" value="<?php echo $staff->code; ?>">
				</div>
				<div class="mb-3">
					<label for="category" class="form-label">Category</label>
					<select id="category" name="category" class="form-select">
						<?php
						$categories = explode(",",setting('staff_categories'));
						
						foreach ($categories as $category) {
							$selected = ($category == $staff->category) ? " selected" : "";
						
							// Output the option tag directly
							echo "<option value='" . htmlspecialchars($category) . "'$selected>" . htmlspecialchars($category) . "</option>";
						}
						?>
					</select>
				</div>
				<div class="mb-3">
					<label for="email" class="form-label">Email Address</label>
					<input type="input" class="form-control" id="email" name="email" value="<?php echo $staff->email; ?>">
				</div>
				<div class="mb-3">
					<input class="form-check-input" type="checkbox" value="1" id="enabled" name="enabled" <?php if ($staff->enabled == "1") { echo " checked"; } ?>>
					  <label class="form-check-label" for="enabled">Enabled</label>
				</div>
				
				<button type="submit" class="btn btn-primary">Submit</button>
				<input type="hidden" name="uid" value="<?php echo $staff->uid; ?>" />
			</form>
		</div>
		<div class="col-4">
			Recent Shifts
			
			<ol class="list-group list-group">
			<?php
			$limit = setting('staff_previous_shifts_display');
			
			$sql = "SELECT * FROM shifts WHERE staff_uid = '" . $staff->uid . "' ORDER BY uid DESC LIMIT " . $limit;
			$openShifts = $db->get($sql);
			
			foreach ($openShifts AS $shift) {
				$shift = new Shift($shift['uid']);
				$staff = new Staff($shift->staff_uid);
				$staffEditURL = "index.php?page=staff_edit&uid=" . $staff->uid;
				
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
				
				$shiftDurationHelper = "";
				if (is_null($shift->shift_end)) {
					$shiftDurationHelper .= " " . icon('hourglass-split');
				}
				
				$output .= $shiftDurationHelper . "<span class=\"badge " . $badgeClass . " rounded-pill\">" . convertMinutesToHours($shift->totalMinutes()) . "</span>";
				$output .= "</li>";
				
				echo $output;
			}
			?>
			</ol>
		</div>
	</div>
</div>

<script>
(() => {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
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