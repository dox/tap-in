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
		<div class="col-8">
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
					<input class="form-check-input" type="checkbox" value="1" id="enabled" name="enabled" <?= ($staff->enabled ?? '') == "1" ? 'checked' : '' ?>>
					<label class="form-check-label" for="enabled">Enabled</label>
				</div>
				
				<input type="hidden" name="uid" value="<?= htmlspecialchars($staff->uid ?? '') ?>" />
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>

		<?php if ($uid): ?>
		<div class="col-4">
			Recent Shifts
			<ol class="list-group">
				<?php
				foreach ($staff->recentShifts() as $shiftData) {
					$shift = new Shift($shiftData['uid']);
					$url = "index.php?page=shift_edit&uid=" . $shift->uid;
					$badgeClass = empty($shift->shift_end) ? "text-bg-primary" : "text-bg-success";
					$hours = convertMinutesToHours($shift->totalMinutes());
					$icon = is_null($shift->shift_end) ? " " . icon('hourglass-split') : "";
					echo <<<HTML
					<li class="list-group-item d-flex justify-content-between align-items-start">
						<div class="ms-2 me-auto">
							<div class="fw-bold">{$staff->fullname()}</div>
							{$shift->shift_start}
						</div>
						$icon<a href="$url"><span class="badge $badgeClass rounded-pill">$hours</span></a>
					</li>
					HTML;
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
