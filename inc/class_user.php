<?php
use LdapRecord\Connection;
use LdapRecord\Auth\BindException;



class User {
	private string $lastError = '';
	
	
	public function isLoggedIn(): bool {
		return ($_SESSION['logged_in'] ?? false) === true;
	}
	
	public function getLastError(): string {
		return $this->lastError;
	}
	
	public function login(string $username, string $password): bool {
		global $log;
		
		$connection = new Connection([
			// Mandatory Configuration Options
			'hosts'            => LDAP_SERVER,
			'base_dn'          => LDAP_BASE_DN,
			'username'         => LDAP_BIND_DN,  // Admin DN for bind
			'password'         => LDAP_BIND_PASSWORD,  // Admin password
		
			// Optional Configuration Options
			'port'             => LDAP_PORT,
			'protocol'         => 'ldap://',
			'use_ssl'          => false,
			'use_tls'          => LDAP_STARTTLS,
			'use_sasl'         => false,
			'version'          => 3,
			'timeout'          => 5,
			'follow_referrals' => false
		]);
	
		try {
			// Step 1: Bind as the admin account
			$connection->auth()->bind(LDAP_BIND_DN, LDAP_BIND_PASSWORD);
			
			// Step 2: Search for the user
			$user = $connection->query()
				->where('samaccountname', '=', $username)
				->first(); // Change to first() to return null if no results
	
			// Step 2a: Check if no user is found
			if ($user === null || empty($user['distinguishedname'][0])) {
				// Handle the case where no user is found
				$this->lastError = 'Username not found or password is incorrect.';
				
				$logData = [
					'category' => 'login',
					'result'   => 'warning',
					'description' => "Login failed for " . $username . ": " . $this->lastError
				];
				$log->create($logData);
				
				return false;
			}
			
			// Step 3: Check the username/password are correct
			if ($connection->auth()->attempt($user['distinguishedname'][0], $password)) {
				// User has been successfully authenticated.
				
				//Step 3a: Check the user is a member of the allowed groups
				$userGroups = $user['memberof'];
				
				// Normalize the group distinguished names and determine if
				// the user is a member of any of the allowed groups:
				$difference = array_intersect(
					array_map('strtolower', $userGroups),
					array_map('strtolower', LDAP_ALLOWED_DN)
				);
				
				if (count($difference) > 0) {
					// The user is a member of one of the allowed groups.
					$_SESSION['logged_in'] = true;
					$_SESSION['username'] = $username;
					
					$logData = [
						'category' => 'login',
						'result'   => 'success',
						'description' => "Login succeeded for " . $username
					];
					$log->create($logData);
					
					return true;
				} else {
					$this->lastError = "You do not have access to this service.";
					
					$logData = [
						'category' => 'login',
						'result'   => 'warning',
						'description' => "Login failed for " . $username . ": " . $this->lastError
					];
					$log->create($logData);
					
					return false;
				}
			} else {
				// Username or password is incorrect.
				$this->lastError = 'Username or password is incorrect.';
				
				$logData = [
					'category' => 'login',
					'result'   => 'warning',
					'description' => "Login failed for " . $username . ": " . $this->lastError
				];
				$log->create($logData);
				return false;
			}
	
		} catch (BindException $e) {
			// Catch binding exceptions and handle specific LDAP errors
			$error = $e->getDetailedError()->getDiagnosticMessage();
		
			if (strpos($error, '532') !== false) {
				$this->lastError = 'Your password has expired.';
			} elseif (strpos($error, '533') !== false) {
				$this->lastError = 'Your account is disabled.';
			} elseif (strpos($error, '701') !== false) {
				$this->lastError = 'Your account has expired.';
			} elseif (strpos($error, '775') !== false) {
				$this->lastError = 'Your account is locked.';
			} else {
				$this->lastError = 'Username or password is incorrect.';
			}
		}
	
		return false;
	}

	public function logout(): void {
		$_SESSION = [];
		session_destroy();
	}

	public function getUsername(): ?string {
		return $_SESSION['username'] ?? null;
	}
	
	private function getUserDn(string $username, $connection) {
		$results = $connection->query()->where('sAMAccountName', '=', $username)->get();
	
		// Check if we have results and if the first result is an object
		if (count($results) == 1) {
			//return $results[0]->getDn();
			return $results[0]['distinguishedname'][0];
		}
	
		// If no valid results are found, return false
		return false;  // If no results found
	}
}