<a href="index.php?page=api">GET</a>
<?php
// Configuration
$apiKey = moorepay_api_key;
$employeeId = '123';
$timetrackingID = '123';

$url = 'https://api02.naturalhr.net/api/v1/employee/' . urlencode($employeeId) . '/time-tracking/' . urlencode($timetrackingID);

// Prepare the timesheet data
$timesheetData = [
	'time_out' => '10:00:00',
	'reference_name' => 'Test'
];

// Initialize cURL
$ch = curl_init($url);
curl_setopt_array($ch, [
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => json_encode($timesheetData),
	CURLOPT_HTTPHEADER => [
		'Authorization: ' . $apiKey,
		'Accept: application/json',
		'Content-Type: application/json'
	],
]);

// Execute the request
$response = curl_exec($ch);
if (curl_errno($ch)) {
	throw new Exception('cURL error: ' . curl_error($ch));
}
curl_close($ch);

// Decode and display the response
$data = json_decode($response, true);

echo "<h2>Timesheet Submission Response</h2><pre>";
print_r($data);
echo "</pre>";
?>


