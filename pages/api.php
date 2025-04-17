<a href="index.php?page=api_post">POST</a>v
<?php
// Configuration
$apiKey = moorepay_api_key;
$employeeId = '123';

$url = 'https://api02.naturalhr.net/api/v1/employee/' . urlencode($employeeId) . '/time-tracking';

// Initialize cURL
$ch = curl_init($url);
curl_setopt_array($ch, [
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => [
		'Authorization: ' . $apiKey,
		'Accept: application/json',
		'Content-type: application/json'
	],
]);

// Execute request
$response = curl_exec($ch);
if (curl_errno($ch)) {
	throw new Exception('cURL error: ' . curl_error($ch));
}
curl_close($ch);

// Parse and output results
$data = json_decode($response, true);

echo "<h2>Time Off Records for Employee #$employeeId</h2><pre>";
print_r($data);
echo "</pre>";
?>