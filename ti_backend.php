<?php

// API Key for Runway ML
$apiKey = 'key_c0249cbf819d7b1c13fbb89bcd056637b1f6dba4a44bbeb5d6ac24e9e06808985521144819ccfaa80d924af6293b2bb4d4448d93d20b4be6807f24edaca5178f';
$apiUrl = 'https://api.runwayml.com/v1/models/gen4_turbo/generate';

// Get the POST data from the frontend (the prompt)
$data = json_decode(file_get_contents('php://input'), true);

// Check if the prompt is valid
if (isset($data['prompt']) && !empty($data['prompt'])) {
    $prompt = $data['prompt'];

    // Prepare the data for the API request
    $postData = [
        'input' => [
            'prompt' => $prompt,
            'num_images' => 1 // You can adjust this to generate more than one image
        ]
    ];

    // Use cURL to send the request to the Runway ML API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $response = curl_exec($ch); // Get the response from the API
    curl_close($ch);

    // Check if the API returned a response
    if ($response) {
        $responseData = json_decode($response, true);

        // Check if the API returned an image URL
        if (isset($responseData['data'][0]['image_url'])) {
            // Success! Return the image URL to the frontend
            echo '<img src="' . $responseData['data'][0]['image_url'] . '" alt="Generated Image" />';
        } else {
            // Something went wrong. Display an error message
            echo "Failed to generate the image. Please try again.";
        }
    } else {
        // If the response is empty or there was an issue with the API call
        echo "An error occurred while contacting the API. Please try again.";
    }
} else {
    // If the prompt is missing or invalid
    echo "No prompt provided. Please enter a valid text prompt.";
}

?>
