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
			$updates[] = "$column = $formattedValue";
		}
		
		// Execute the query
		$result = $stmt->execute();
		if ($result == 1) {
			$log->create([
				'category'    => $table,
				'result'      => 'success',
				'description' => sprintf(
					'Inserted into table %s with values: %s where uid = %s',
					$table,
					implode(", ", $updates),
					$whereValue
				),
			]);
		} else {
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
		
		// Dynamically build the column-value pairs for the SET part of the query
		$setParts = [];
		foreach ($data as $column => $value) {
			// Check if the value is empty and treat it as NULL
			if ($value === null) {
				$setParts[] = "$column = :$column";
				$data[$column] = null;
			} else {
				$setParts[] = "$column = :$column";
			}
		}
		$setClause = implode(", ", $setParts);
		
		// Prepare the SQL query
		$sql = "UPDATE $table SET $setClause WHERE $whereColumn = :whereValue";
		$stmt = $this->pdo->prepare($sql);
		
		// Bind the parameters dynamically for the SET part
		$updates = [];
		foreach ($data as $column => $value) {
			// Bind the value (including null if the value is empty)
			$stmt->bindValue(":$column", $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
			
			$formattedValue = is_scalar($value) ? (string)$value : json_encode($value);
			$updates[] = "$column = $formattedValue";
		}
	
		// Bind the WHERE clause parameter
		$stmt->bindValue(':whereValue', $whereValue);
		
		// Execute the query
		$result = $stmt->execute();
		if ($result == 1) {
			$log->create([
				'category'    => $table,
				'result'      => 'success',
				'description' => sprintf(
					'Updated table %s with values: %s where uid = %s',
					$table,
					implode(", ", $updates),
					$whereValue
				),
			]);
		} else {
			$log->create([
				'category'    => $table,
				'result'      => 'danger',
				'description' => sprintf(
					'Attempted to update table %s with values: %s where uid = %s',
					$table,
					implode(", ", $updates),
					$whereValue
				),
			]);
		}
		
		// return the result (true/false)
		return $result;
	}
}

// Usage
$db = new Database();