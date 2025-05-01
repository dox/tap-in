<?php
class Database {
	private $host = db_host;
	private $dbname = db_name;
	private $username = db_username;
	private $password = db_password;
	private $pdo;

	public function __construct() {
		try {
			$this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set the error mode
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
	
	// Expose the PDO prepare method so it can be used directly
	public function prepare($sql) {
		return $this->pdo->prepare($sql);
	}

	// Example query method
	public function query($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function get($sql, $params = [], $single = false) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		
		if ($single) {
			return $stmt->fetch(PDO::FETCH_ASSOC); // return single row
		} else {
			return $stmt->fetchAll(PDO::FETCH_ASSOC); // return multiple rows
		}
	}
	
	public function create($table, $data) {
		global $log;
		
		// Dynamically build the columns and placeholders
		$columns = implode(", ", array_keys($data));
		$placeholders = ":" . implode(", :", array_keys($data));
	
		// Prepare the SQL query
		$sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
		$stmt = $this->pdo->prepare($sql);
	
		// Bind the parameters dynamically
		$updates = [];
		foreach ($data as $key => $value) {
			$stmt->bindValue(":$key", $value);
			
			$formattedValue = is_scalar($value) ? (string)$value : json_encode($value);
			$updates[] = "$key = $formattedValue";
		}
		
		// Execute the query
		$result = $stmt->execute();
		if ($result != 1) {
			$log->create([
				'category'    => $table,
				'result'      => 'danger',
				'description' => sprintf(
					'Attempted to insert into table %s with values: %s where uid = %s',
					$table,
					implode(", ", $updates),
					$whereValue
				),
			]);
		}
		
		// return the result (true/false)
		return $result;
	}
	
	public function update($table, $data, $whereColumn, $whereValue) {
		global $log;
	
		// Fetch current row from database
		$sqlSelect = "SELECT * FROM $table WHERE $whereColumn = :whereValue";
		$stmtSelect = $this->pdo->prepare($sqlSelect);
		$stmtSelect->bindValue(':whereValue', $whereValue);
		$stmtSelect->execute();
		$currentRow = $stmtSelect->fetch(PDO::FETCH_ASSOC);
	
		if (!$currentRow) {
			$log->create([
				'category'    => $table,
				'result'      => 'danger',
				'description' => "Record not found for update where $whereColumn = $whereValue",
			]);
			return false;
		}
	
		// Determine which values have changed
		$setParts = [];
		$updates = [];
		$params = [];
		foreach ($data as $column => $newValue) {
			$currentValue = $currentRow[$column] ?? null;
	
			// Normalize nulls and string types for fair comparison
			if ($newValue === null) $newValue = null;
			if ($currentValue === null) $currentValue = null;
	
			// Compare using stringified values for general cases
			if ((string)$currentValue !== (string)$newValue) {
				$setParts[] = "$column = :$column";
				$params[":$column"] = $newValue;
				$updates[] = "$column = " . (is_scalar($newValue) ? (string)$newValue : json_encode($newValue));
			}
		}
	
		// If nothing has changed, skip the update
		if (empty($setParts)) {
			return true; // nothing to do, but not a failure
		}
	
		// Build and run the update query
		$setClause = implode(", ", $setParts);
		$sqlUpdate = "UPDATE $table SET $setClause WHERE $whereColumn = :whereValue";
		$stmtUpdate = $this->pdo->prepare($sqlUpdate);
	
		// Bind changed values
		foreach ($params as $param => $value) {
			$stmtUpdate->bindValue($param, $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		}
		$stmtUpdate->bindValue(':whereValue', $whereValue);
	
		$result = $stmtUpdate->execute();
		
		return $result;
	}
	
	public function delete($table, $whereColumn, $whereValue) {
		global $log;
	
		// Build the SQL DELETE statement
		$sql = "DELETE FROM $table WHERE $whereColumn = :value";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':value', $whereValue);
	
		// Execute the query
		$result = $stmt->execute();
	
		// Log the result
		if ($result != 1) {
			$log->create([
				'category'    => $table,
				'result'      => 'danger',
				'description' => sprintf(
					'Failed to delete from table %s where %s = %s',
					$table,
					$whereColumn,
					$whereValue
				),
			]);
		}
	
		return $result;
	}
}

// Usage
$db = new Database();