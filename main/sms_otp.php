<?php

$requestData = json_decode(file_get_contents("php://input"), true);
$phoneNumber = $requestData['phone'];

$otp = rand(100000, 999999);

$apiToken = '084ee9d03ee2c8d4188c7ece60a665fa8e673520';
$message = "Your OTP code is $otp. Do not share this code with anyone.";

function getSmsProvider($phoneNumber) {
    
    $number = preg_replace('/[^0-9]/', '', $phoneNumber);
    
    if (substr($number, 0, 2) === '63') {
        $number = '0' . substr($number, 2);
    }
    
    if (strlen($number) < 11 || substr($number, 0, 1) !== '0') {
        return 1; 
    }
    
    $prefix = substr($number, 1, 3); 
    
    $globePrefixes = ['817', '905', '906', '915', '916', '917', '926', '927', '935', '936', '937', '945', '953', '954', '955', '956', '965', '966', '967', '975', '977', '978', '979', '995', '996', '997'];
    $smartPrefixes = ['813', '907', '908', '909', '910', '911', '912', '918', '919', '920', '921', '928', '929', '930', '931', '938', '939', '940', '941', '942', '943', '944', '946', '947', '948', '949', '950', '951', '961', '973', '974', '979', '989', '991', '992', '993', '994', '998', '999'];
    $ditoPrefixes = ['895', '896', '897', '898', '899'];

    if (in_array($prefix, $globePrefixes)) {
        return 2; 
    } elseif (in_array($prefix, $smartPrefixes)) {
        return 1; 
    } elseif (in_array($prefix, $ditoPrefixes)) {
        return 3; 
    }
    
    return 1; 
}

$smsProvider = getSmsProvider($phoneNumber);

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
        'otp' => $otp 
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Failed to send OTP. API Response: $response"
    ]);
}
?>