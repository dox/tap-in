<?php
$shift = new Shift($_GET['uid']);
$staff = new Staff($shift->staff_uid);
$staffAll = $db->get("SELECT * FROM staff ORDER BY lastname ASC");
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<h1><?php echo icon('hourglass-split', '1em'); ?> Shift Edit for <?php echo $staff->fullname(); ?></h1>

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
	</div>
	<div class="mb-3">
		<label for="shift_start" class="form-label">Shift Start Time</label>
		<input type="input" class="form-control" id="shift_start" name="shift_start" value="<?php echo $shift->shift_start; ?>">
		
	</div>
	<div class="mb-3">
		<label for="shift_end" class="form-label">Shift End Time</label>

		<div class="input-group">
			<input type="input" class="form-control" id="shift_end" name="shift_end" value="<?php echo $shift->shift_end; ?>">
			<button class="btn btn-outline-secondary" type="button" id="clearEndDate">Clear</button>
		</div>
		
		
	</div>
	
	<button type="submit" class="btn btn-primary">Submit</button>
	<button type="submit" name="delete" value="1" class="btn btn-danger" onclick="return confirmDelete();">Delete</button>
	<input type="hidden" name="uid" value="<?php echo $shift->uid; ?>" />
</form>

<script>
const minEndDate = "<?php echo $shift->shift_start; ?>";

const shift_startPicker = flatpickr("#shift_start", {
	enableTime: true,
	dateFormat: "Y-m-d H:i",
	time_24hr: true,
	minuteIncrement: 1,
	onChange: function(selectedDates) {
		if (selectedDates.length > 0) {
			// Set the minimum date of the end picker to the selected start date
			shift_EndPicker.set("minDate", selectedDates[0]);
	
			// Optional: If end is before start, clear it
			if (shift_EndPicker.selectedDates[0] && shift_EndPicker.selectedDates[0] < selectedDates[0]) {
				shift_EndPicker.clear();
			}
		}
	}
});
const shift_EndPicker = flatpickr("#shift_end", {
	enableTime: true,
	dateFormat: "Y-m-d H:i",
	time_24hr: true,
	minuteIncrement: 1,
	minDate: minEndDate
});

document.getElementById("clearEndDate").addEventListener("click", function () {
	shift_EndPicker.clear();
});

function confirmDelete() {
	return confirm("Are you absolutely sure you want to delete this shift?  This action cannot be undone!");
}
</script>