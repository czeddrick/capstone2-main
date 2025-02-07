<?php
// Read JSON input
$requestData = json_decode(file_get_contents("php://input"), true);
$phoneNumber = $requestData['phone'];
$smsProvider = $requestData['sms_provider'] ?? 1; // Default to 0 if not provided

// Generate a random 6-digit OTP
$otp = rand(100000, 999999);

// Replace with your actual iProg API token
$apiToken = '084ee9d03ee2c8d4188c7ece60a665fa8e673520';
$message = "Your OTP code is $otp. Do not share this code with anyone.";

// Prepare data with the SMS provider field
$data = [
    'api_token' => $apiToken,
    'message' => $message,
    'phone_number' => $phoneNumber,
    'sms_provider' => $smsProvider
];

$url = 'https://sms.iprogtech.com/api/v1/sms_messages';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode([
        'success' => true,
        'otp' => $otp // Return OTP for testing only; remove in production
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Failed to send OTP. API Response: $response"
    ]);
}
?>
