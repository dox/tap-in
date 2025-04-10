<?php
require_once 'inc/auth.php';
$user->logout();
header("Location: login.php");
exit;