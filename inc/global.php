<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function setting($name) {
	global $db;
	
	$sql = "SELECT * FROM settings WHERE name = :name LIMIT 1";
	
	$result = $db->query($sql, [':name' => $name]);
	
	if (empty($result)) {
		return false;
	} else {
		return $result[0]['value'];
	}
}

function alert($type, $title, $content) {
	// List of valid Bootstrap alert types
	$validTypes = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

	// Ensure the provided type is valid, default to 'info' if invalid
	if (!in_array($type, $validTypes)) {
		$type = 'info';  // Default type if the passed type is not valid
	}

	// Generate the alert HTML
	$output  = "<div class=\"alert alert-$type alert-dismissible fade show\" role=\"alert\">";
	$output .= "<strong>" . ucfirst($title) . "</strong> "; // Capitalize the alert type
	$output .= $content;
	$output .= "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>";
	$output .= "</div>";
	
	return $output;
}

function icon(string $iconName, string $size = '1em'): string {
	$iconPath = 'icons/' . $iconName . '.svg';  // Path to the icon file

	// Check if the requested SVG file exists
	if (file_exists($iconPath)) {
		// Load the SVG content
		$svgContent = file_get_contents($iconPath);

		// Replace the width and height attributes with the desired size
		$svgContent = preg_replace('/(width|height)="\d+(\.\d+)?"/', '$1="' . $size . '"', $svgContent);

		return $svgContent;
	}

	// If the file doesn't exist, return a default SVG
	$defaultIconPath = 'icons/question-diamond.svg';  // Path to the default SVG
	if (file_exists($defaultIconPath)) {
		$defaultSvgContent = file_get_contents($defaultIconPath);
		
		// Adjust size of the default SVG
		$defaultSvgContent = preg_replace('/(width|height)="\d+(\.\d+)?"/', '$1="' . $size . '"', $defaultSvgContent);

		return $defaultSvgContent;
	}

	// If the default SVG is missing as well, return a simple placeholder SVG
	return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '"><rect width="100%" height="100%" fill="gray" /></svg>';
}


function shiftDurationMinutes($shift_start, $shift_end = null) {
	$start = new DateTime($shift_start);
	$end = $shift_end ? new DateTime($shift_end) : new DateTime();

	$interval = $start->diff($end);
	
	return ($interval->d * 24 * 60) + ($interval->h * 60) + $interval->i;
}

function convertMinutesToHours($minutes) {
	$hours = floor($minutes / 60); // Get the whole hours
	$minutes = $minutes % 60; // Get the remaining minutes
	return sprintf('%02d:%02d', $hours, $minutes); // Format as hh:mm
}

?>
