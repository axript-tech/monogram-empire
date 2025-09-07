<?php

/**
 * Sends a WhatsApp message using the Termii API.
 * * Make sure to replace the placeholder values for
 * api_key, to, and from with your actual data.
 */

// --- Configuration ---
// Replace with your actual Termii API key from your dashboard.
$apiKey = 'TLnWDlFgkDRRTlaEEyQqIvODikxdGopXPmQOCLUqxkWeBrGLYxnHIXMYymVWIn'; 

// Replace with the recipient's phone number in international format (e.g., 2348012345678).
$recipientNumber = '2348104041253'; 

// Replace with your approved Termii Sender ID.
$senderId = 'YOUR_SENDER_ID';

// The message content you want to send.
$message = 'Hello from PHP! Your verification code is 12345.';
// --- End of Configuration ---

// Termii API endpoint for sending messages
$url = 'https://api.ng.termii.com/api/sms/send';

// The data payload for the API request.
$data = [
    'to' => $recipientNumber,
    'from' => $senderId,
    'sms' => $message,
    'type' => 'plain',
    'channel' => 'whatsapp', // This is crucial for sending via WhatsApp
    'api_key' => $apiKey,
];

// Initialize cURL session.
$ch = curl_init($url);

// Set cURL options for the POST request.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string instead of outputting it.
curl_setopt($ch, CURLOPT_POST, true);           // Specify that this is a POST request.
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Attach the JSON-encoded data.
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json' // Set the content type header.
]);

// Execute the cURL request.
$response = curl_exec($ch);
$error = curl_error($ch); // Check for cURL errors.

// Close the cURL session.
curl_close($ch);

// --- Handle Response ---
header('Content-Type: application/json'); // Set the output content type to JSON for clear viewing.

if ($error) {
    // If there was a cURL error (e.g., network issue), output the error.
    echo json_encode([
        'status' => 'error',
        'message' => 'cURL Error: ' . $error
    ]);
} else {
    // If the request was successful, output the response from the Termii API.
    // A successful response will contain a message_id and status.
    echo $response;
}

?>
