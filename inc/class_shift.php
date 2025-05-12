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
		
		if (isset($this->shift_end) && date('Y-m-d', strtotime($this->shift_start)) != date('Y-m-d', strtotime($this->shift_end))) {
			$trClass = "table-warning";
		} else {
			$trClass = "";
		}
		
		// Return the table row as a string, directly building it
		return '<tr class="' . $trClass . '">'
			. '<th scope="row"><a href="' . $staffURL . '">' . htmlspecialchars($staff->fullname()) . '</a></th>'
			. '<td>' . dateDisplay($this->shift_start, true) . '</td>'
			. '<td>' . dateDisplay($this->shift_end, true) . '</td>'
			. '<td>' . $shiftDurationHelper . convertMinutesToHours($shiftDuration) . '</td>'
			. '<td><a href="' . $shiftEditURL . '">' . icon('pencil-square') . '</a></td>'
			. '</tr>';
	}
	
	public function listGroupItem() {
		$staff = new Staff($this->staff_uid);
		$staffEditURL = "index.php?page=staff_edit&uid=" . $staff->uid;
		
		$output  = "<li class=\"list-group-item d-flex justify-content-between align-items-start\">";
		$output .= "<div class=\"ms-2 me-auto\">";
		$output .= "<div class=\"fw-bold\"><a href=\"" . $staffEditURL . "\">" . $staff->fullname() . "</a></div>";
		$output .= $this->shift_start;
		$output .= "</div>";
		$output .= $this->listGroupItemDurationBadge();
		
		$output .= "</li>";
		
		return $output;
	}
	
	private function listGroupItemDurationBadge() {
		$shiftEditURL = "index.php?page=shift_edit&uid=" . $this->uid;
		$icon = is_null($this->shift_end) ? " " . icon('hourglass-split') : "";
		
		if (empty($this->shift_end)) {
			if (date('Y-m-d', strtotime($this->shift_start)) < date('Y-m-d')) {
				$badgeClass = "text-bg-danger";
			} else {
				$badgeClass = "text-bg-primary";
			}
		} else {
			if (date('Y-m-d', strtotime($this->shift_start)) != date('Y-m-d', strtotime($this->shift_end))) {
				$badgeClass = "text-bg-warning";
			} else {
				$badgeClass = "text-bg-success";
			}
		}
		
		$output = $icon . "<a href=\"" . $shiftEditURL . "\"><span class=\"badge " . $badgeClass . " rounded-pill\">" . convertMinutesToHours($this->totalMinutes()) . "</a></span>";
		
		return $output;
	}
	
	public function totalMinutes() {
		return shiftDurationMinutes($this->shift_start, $this->shift_end);
	}
	
	public function totalMinutesRoundedUp() {
		$roundUp = setting('shift_roundup');
		
		return ceil($this->totalMinutes() / $roundUp) * $roundUp;
	}
}
