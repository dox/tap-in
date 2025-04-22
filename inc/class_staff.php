<?php
class Staff {
	public static $table_name = 'staff';
	
	public $uid;
	public $enabled;
	public $firstname;
	public $lastname;
	public $code;
	public $category;
	public $email;
	public $payroll_id;
	public $last_tapin;
	
	public function __construct($lookup = null) {
		global $db;
	
		// Case where $lookup is an array with either 'uid' or 'code'
		if (is_array($lookup)) {
			// If both 'uid' and 'code' are set, prioritize either (here we prioritize 'uid')
			if (isset($lookup['uid'])) {
				$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = :value LIMIT 1";
				$result = $db->get($sql, [':value' => $lookup['uid']], true);
			} elseif (isset($lookup['code'])) {
				$sql = "SELECT * FROM " . self::$table_name . " WHERE code = :value LIMIT 1";
				$result = $db->get($sql, [':value' => $lookup['code']], true);
			}
		} else {
			// Case where $lookup is a single value (numeric), we treat it as either 'uid' or 'code'
			if (is_numeric($lookup)) {
				$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = :value LIMIT 1";
				$result = $db->get($sql, [':value' => $lookup], true);
			} else {
				$sql = "SELECT * FROM " . self::$table_name . " WHERE code = :value LIMIT 1";
				$result = $db->get($sql, [':value' => $lookup], true);
			}
		}
	
		// If record found, populate the class properties
		if ($result) {
			foreach ($result as $key => $value) {
				if (property_exists($this, $key)) {
					$this->$key = $value;
				}
			}
		}
	}
	
	public function fullname() {
		return $this->lastname . ", " . $this->firstname;
	}
	
	public function tapin() {
		global $db, $log;
		
		if (!$this->currentShiftUID()) {
			$shiftData = [
				'staff_uid'    => $this->uid,
				'shift_start'  => date('c')
			];
			$success = $db->create('shifts', $shiftData);
			
			// update the last tap-in date for this user
			$staffData['last_tapin'] = date('c');
			$db->update('staff', $staffData, 'uid', $this->uid);
			
			return true;
		} else {
			$logData = [
				'category' => 'staff',
				'result'   => 'warning',
				'description' => 'Attempted to tapin when there was already an open shift for ' . $this->fullname()
			];
			$log->create($logData);
			
			return false;
		}
	}
	
	public function tapout() {
		global $db, $log;
		
		if ($this->currentShiftUID()) {
			$shiftData = [
				'staff_uid'    => $this->uid,
				'shift_end'  => date('c')
			];
			$success = $db->update('shifts', $shiftData, 'uid', $this->currentShiftUID());
			
			// update the last tap-in date for this user
			$staffData['last_tapin'] = date('c');
			$db->update('staff', $staffData, 'uid', $this->uid);
			
			return true;
		} else {
			$logData = [
				'category' => 'staff',
				'result'   => 'warning',
				'description' => 'Attempted to close shift when no open shift existed for ' . $this->fullname()
			];
			$log->create($logData);
			
			return false;
		}
	}
	
	public function currentShiftUID() {
		global $db;
		
		$checkSql = "SELECT uid FROM shifts 
					 WHERE staff_uid = :staff_uid AND shift_end IS NULL 
					 LIMIT 1";
		
		$openShift = $db->query($checkSql, [':staff_uid' => $this->uid]);
		
		if (empty($openShift)) {
			return false;
		} else {
			$shift = new Shift($openShift[0]['uid']);
			
			return $shift->uid;
		}
	}
	
	public function recentShifts() {
		global $db;
		
		$limit = setting('staff_previous_shifts_display');
		
		$sql = "SELECT * FROM shifts WHERE staff_uid = '" . $this->uid . "' ORDER BY uid DESC LIMIT " . $limit;
		
		$shifts = $db->get($sql);
		
		return $shifts;
	}
	
	
	public function totalMinutes() {
		return $this->totalMinutesBetweenDates();
	}
	
	public function totalMinutesBetweenDates($from = null, $to = null) {
		global $db;
	
		// Start building the SQL query
		$sql = "SELECT SUM(
				TIMESTAMPDIFF(MINUTE, shift_start, IFNULL(shift_end, NOW()))
			) AS total_minutes
			FROM shifts
			WHERE staff_uid = '" . $this->uid . "'";
	
		// Add date range conditions if provided
		if ($from) {
			$sql .= " AND shift_start >= '" . $from . "'";
		}
	
		if ($to) {
			$sql .= " AND shift_start <= '" . $to . "'";
		}
	
		// Execute the query
		$shiftsSum = $db->get($sql);
	
		// Check if there's a result and set total accordingly
		if ($shiftsSum[0]['total_minutes'] > 0) {
			$total = $shiftsSum[0]['total_minutes'];
		} else {
			$total = 0;
		}
	
		return $total;
	}
	
	public function tableRow() {
		$staffURL = "index.php?page=staff_edit&uid=" . $this->uid;
		
		$class = "";
		if ($this->enabled == 0) {
			$class = "table-secondary";
		}
		// Return the table row as a string, directly building it
		return '<tr class=' . $class . '>'
			. '<th scope="row"><a href="' . $staffURL . '">' . htmlspecialchars($this->fullname()) . '</a></th>'
			. '<td><kbd>' . htmlspecialchars($this->code) . '</kbd></td>'
			. '<td>' . dateDisplay($this->last_tapin, true) . '</td>'
			. '</tr>';
	}
	
	public function updateLastTapin() {
		global $db;
	
		// Prepare the SQL query
		$sql = "UPDATE " . self::$table_name . " 
				SET last_tapin = :date 
				WHERE uid = :uid 
				LIMIT 1";
	
		// Prepare the statement and execute with parameters
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':date', date('c'));
		$stmt->bindParam(':uid', $this->uid, PDO::PARAM_INT);
	
		return $stmt->execute();
	}

}
