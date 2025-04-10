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
		// Dynamically build the columns and placeholders
		$columns = implode(", ", array_keys($data));
		$placeholders = ":" . implode(", :", array_keys($data));
	
		// Prepare the SQL query
		$sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
		$stmt = $this->pdo->prepare($sql);
	
		// Bind the parameters dynamically
		foreach ($data as $key => $value) {
			$stmt->bindValue(":$key", $value);
		}
	
		// Execute the query and return the result (true/false)
		return $stmt->execute();
	}
	
	public function update($table, $data, $whereColumn, $whereValue) {
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
		foreach ($data as $column => $value) {
			// Bind the value (including null if the value is empty)
			$stmt->bindValue(":$column", $value, $value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		}
	
		// Bind the WHERE clause parameter
		$stmt->bindValue(':whereValue', $whereValue);
		
		// Execute the query and return the result (true/false)
		return $stmt->execute();
	}
}

// Usage
$db = new Database();