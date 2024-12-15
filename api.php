<?php
$userID = "3319854";
$url = "https://sandbox.api.mailtrap.io/api/send/$userID";
$apiToken = "3ba5ca3645eaf3c94965e6c30323020a";

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'to' => [
            [
                'email' => 'john_doe@example.com',
                'name' => 'John Doe'
            ]
        ],
        'from' => [
            'email' => 'yourmail@gmail.com',
            'name' => 'Example Sales Team'
        ],
        'reply_to' => [
            'email' => 'reply@example.com',
            'name' => 'Reply'
        ],
        'attachments' => [],
        'custom_variables' => [
            'user_id' => '45982',
            'batch_id' => 'PSJ-12'
        ],
        'headers' => [
            'X-Message-Source' => 'dev.mydomain.com'
        ],
        'subject' => 'Your Example Order Confirmation',
        'text' => 'Congratulations on your order no. 1234',
        'category' => 'API Test'
    ]),
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "Authorization: Bearer $apiToken",
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}