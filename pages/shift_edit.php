<?php
$shift = new Shift($_GET['uid']);
$staff = new Staff($shift->staff_uid);
$staffAll = $db->get("SELECT * FROM staff ORDER BY lastname ASC");
?>

<h1>Shift Edit for <?php echo $staff->fullname(); ?></h1>

<form method="POST" action="index.php?page=shifts">
	<div class="mb-3">
		<label for="staff_uid" class="form-label">Staff Member</label>
		<select id="staff_uid" name="staff_uid" class="form-select">
			<?php
			$staffAll = $db->get("SELECT * FROM staff ORDER BY lastname ASC");

			foreach ($staffAll as $staffOption) {
				$selected = ($staffOption['uid'] == $staff->uid) ? " selected" : "";
			
				// Output the option tag directly
				echo "<option value='" . htmlspecialchars($staffOption['uid']) . "'$selected>" . htmlspecialchars($staffOption['lastname']) . ", " . htmlspecialchars($staffOption['firstname']) . "</option>";
			}
			?>
		</select>
		<div id="emailHelp" class="form-text">Helper text.</div>
	</div>
	<div class="mb-3">
		<label for="shift_start" class="form-label">Shift Start Time</label>
		<input type="input" class="form-control" id="shift_start" name="shift_start" value="<?php echo $shift->shift_start; ?>">
	</div>
	<div class="mb-3">
		<label for="shift_end" class="form-label">Shift End Time</label>
		<input type="input" class="form-control" id="shift_end" name="shift_end" value="<?php echo $shift->shift_end; ?>">
	</div>
	
	<button type="submit" class="btn btn-primary">Submit</button>
	<input type="hidden" name="uid" value="<?php echo $shift->uid; ?>" />
</form>