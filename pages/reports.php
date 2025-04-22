<h1><?php echo icon('cloud-download', '1em'); ?> Reports</h1>

<?php
// Generate the months array (current + previous 12 months)
$months = [];
for ($i = 0; $i <= 12; $i++) {
	$start = new DateTime("first day of -$i month");
	$end = new DateTime("last day of -$i month");

	$label = $start->format('F Y'); // e.g., "April 2025"
	$value = $start->format('Y-m-d') . '|' . $end->format('Y-m-d'); // send both start and end

	$months[] = [
		'label' => $label,
		'value' => $value
	];
}
?>
<div class="row row-cols-1 row-cols-md-3 g-4">
	<div class="col">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">Totals</h5>
				<p class="card-text">An export of staff and their total worked hours</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<form action="export.php?page=totals" method="post">
						<div class="input-group">
							<select class="form-select" aria-label="Default select example" name="date_range" id="date_range">
								<option selected>Select month...</option>
								<?php foreach ($months as $month): ?>
									<option value="<?= htmlspecialchars($month['value']) ?>">
									<?= htmlspecialchars($month['label']) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<button class="btn btn-outline-secondary" type="submit">Go</button>
						</div>
					</form>
				</li>
			</ul>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">Staff</h5>
				<p class="card-text">An export of all staff</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<form action="export.php?page=staff" method="post">
						<button class="btn w-100 btn-outline-secondary" type="submit">Go</button>
					</form>
				</li>
			</ul>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">Shifts</h5>
				<p class="card-text">An export of all shifts</p>
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<form action="export.php?page=shifts" method="post">
						<div class="input-group">
							<select class="form-select" aria-label="Default select example" name="date_range" id="date_range">
								<option selected>Select month...</option>
								<?php foreach ($months as $month): ?>
									<option value="<?= htmlspecialchars($month['value']) ?>">
									<?= htmlspecialchars($month['label']) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<button class="btn btn-outline-secondary" type="submit">Go</button>
						</div>
					</form>
				</li>
			</ul>
		</div>
	</div>
</div>