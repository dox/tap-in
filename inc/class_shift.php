<?php
class Shift {
	public static $table_name = 'shifts';
	
	public $uid;
	public $staff_uid;
	public $shift_start;
	public $shift_end;
	
	public function __construct($uid = null) {
		global $db;
	
		if ($uid !== null) {
			$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = :uid LIMIT 1";
			$result = $db->get($sql, [':uid' => $uid], true);
	
			if ($result) {
				foreach ($result as $key => $value) {
					if (property_exists($this, $key)) {
						$this->$key = $value;
					}
				}
			}
		}
	}
	
	public function tableRow() {
		$staff = new Staff($this->staff_uid);
		$shiftDuration = shiftDurationMinutes($this->shift_start, $this->shift_end);
		
		$shiftEditURL = "index.php?page=shift_edit&uid=" . $this->uid;
		$staffURL = "index.php?page=staff_edit&uid=" . $staff->uid;
		
		$shiftDurationHelper = "";
		if (is_null($this->shift_end)) {
			$shiftDurationHelper .= " " . icon('hourglass-split');
		}
		
		// Return the table row as a string, directly building it
		return '<tr>'
			. '<th scope="row"><a href="' . $staffURL . '">' . htmlspecialchars($staff->fullname()) . '</a></th>'
			. '<td>' . dateDisplay($this->shift_start, true) . '</td>'
			. '<td>' . dateDisplay($this->shift_end, true) . '</td>'
			. '<td>' . $shiftDurationHelper . convertMinutesToHours($shiftDuration) . '</td>'
			. '<td><a href="' . $shiftEditURL . '">' . icon('pencil-square') . '</a></td>'
			. '</tr>';
	}
	
	public function totalMinutes() {
		return shiftDurationMinutes($this->shift_start, $this->shift_end);
	}

}
