<?php
session_start();

require_once __DIR__ . '/class_user.php';

$user = new User();

// Optional: redirect if not logged in
function requireLogin() {
	global $user;
	if (!$user->isLoggedIn()) {
		header("Location: login.php");
		exit;
	}
}