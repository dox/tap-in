<?php
class Logs {
	public static $table_name = 'logs';

	private function getRequestIpAsInteger() {
		if (PHP_SAPI === 'cli') {
			return 0;
		}

		$remoteAddress = $_SERVER['REMOTE_ADDR'] ?? null;
		$ipAsInteger = $remoteAddress ? ip2long($remoteAddress) : false;

		return $ipAsInteger !== false ? $ipAsInteger : 0;
	}

	private function getLogUsername() {
		if (!empty($_SESSION['username'])) {
			return $_SESSION['username'];
		}

		return PHP_SAPI === 'cli' ? 'cli' : null;
	}
	
	public function create($array = null) {
		global $db;  // Assuming $db is the instance of your Database class

		$description = $array['description'] ?? '';

		$sql = "INSERT INTO " . self::$table_name . " (ip, username, category, result, description) 
				VALUES (:ip, :username, :category, :result, :description)";

		$params = [
			':ip' => $this->getRequestIpAsInteger(),
			':username' => $this->getLogUsername(),
			':category' => $array['category'] ?? 'general',
			':result' => $array['result'] ?? 'info',
			':description' => $description,
		];

		$stmt = $db->prepare($sql);

		return $stmt->execute($params);
	}
	
	public function get() {
		global $db;
	
		// Get the maximum log age from settings
		$maximumLogsDisplay = date('Y-m-d', strtotime('-' . setting('logs_display') . ' days'));
	
		// Prepare the SQL query
		$sql  = "SELECT uid, INET_NTOA(ip) AS ip, username, date, result, category, description  
				 FROM " . self::$table_name . " 
				 WHERE DATE(date) > :maximumLogsAge 
				 ORDER BY date DESC";
		
		// Execute the query with the bound parameter
		$results = $db->query($sql, ['maximumLogsAge' => $maximumLogsDisplay]);
		
		return $results;
	}
	
	public function purge() {
		global $db;
		
		$maximumLogsAge = setting('logs_retention');
	
		// SQL to delete logs older than logs_retention days
		$sql = "DELETE FROM " . self::$table_name . " 
				WHERE date < DATE_SUB(NOW(), INTERVAL " . $maximumLogsAge . " DAY)";
	
		return $db->query($sql);
	}
	
	public function table($logs = null) {
		$table  = "<table class=\"table\">";
		$table .= "<thead>";
		$table .= "<tr>";
		$table .= "<th scope=\"col\">Date</th>";
		$table .= "<th scope=\"col\">IP</th>";
		$table .= "<th scope=\"col\">Username</th>";
		$table .= "<th scope=\"col\">Category</th>";
		$table .= "<th scope=\"col\">Description</th>";
		$table .= "</tr>";
		$table .= "</thead>";
		$table .= "<tbody>";
		
		foreach ($logs as $log) {
			$table .= self::tableRow($log);
		}
		
		$table .= "</tbody>";
		$table .= "</table>";
		
		return $table;
	}

	private function renderDescription($log) {
		$description = htmlspecialchars($log['description']);

		if (($log['category'] ?? '') !== 'shift') {
			return $description;
		}

		$pattern = '/\bshift\s+#(\d+)\b/i';
		$description = preg_replace_callback($pattern, function ($matches) {
			$shiftUid = $matches[1];
			$url = 'index.php?page=shift_edit&uid=' . urlencode($shiftUid);

			return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">shift #' . htmlspecialchars($shiftUid, ENT_QUOTES, 'UTF-8') . '</a>';
		}, $description);

		return $description;
	}
	
	private function tableRow($log = null) {
		// Initialize the row class based on the result
		switch ($log['result']) {
			case 'error':
				$class = 'table-danger';
				break;
			case 'warning':
				$class = 'table-warning';
				break;
			case 'success':
				$class = 'table-success';
				break;
			default:
				$class = '';  // No class if result is something else
				break;
		}
	
		// Return the table row as a string, directly building it
		return '<tr class="' . $class . '">'
			. '<th scope="row">' . htmlspecialchars($log['date']) . '</th>'
			. '<td>' . htmlspecialchars($log['ip']) . '</td>'
			. '<td>' . htmlspecialchars($log['username']) . '</td>'
			. '<td>' . htmlspecialchars($log['category']) . '</td>'
			. '<td>' . $this->renderDescription($log) . '</td>'
			. '</tr>';
	}

}

$log = new Logs();
