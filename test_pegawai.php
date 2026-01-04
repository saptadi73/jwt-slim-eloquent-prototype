<?php

// Test script to create pegawai
$curl = curl_init();

// Prepare multipart form data
$data = [
    'nama' => 'Test Employee',
    'alamat' => 'Jl. Test 123',
    'hp' => '081234567890',
    'email' => 'test@example.com',
    'departemen_id' => null,
    'group_id' => null,
    'position_id' => null,
];

curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost:8080/api/pegawai',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJjZjNkMjEyNC0wMzVjLTQwOTItODUwYi1lN2EyMmFjODliOTUiLCJuYW1lIjoic2FwdGFkaSIsImVtYWlsIjoic2FwdGFkaUB5YWhvby5jb20iLCJpYXQiOjE3Njc1MDQ5NTgsImV4cCI6MTc2NzU0ODE1OH0.ICywxqtoAh7HGPWIsGWj_nEGOoETeHKTMEUL8Oi-OS0'
    ]
]);

$response = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

if ($error) {
    echo "CURL Error: $error\n";
} else {
    echo "Response:\n";
    echo json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
