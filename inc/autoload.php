<?php
include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../config.php';


if (debug) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

include __DIR__ . '/global.php';
include __DIR__ . '/database.php';
include __DIR__ . '/class_settings.php';
include __DIR__ . '/class_logs.php';
include __DIR__ . '/class_staff.php';
include __DIR__ . '/class_shift.php';
include __DIR__ . '/auth.php';

#include __DIR__ . '/user.php';





?>