<?php
header("Content-Type: application/json");

// Check if POST data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $text = $_POST['text'];

    // Replace with your own DeepAI API key
    $api_key = 'a02532ce-4660-404d-9807-02027762bc9d'; 
    $url = 'https://api.deepai.org/api/text2img';

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['text' => $text]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $api_key
    ]);

    // Execute cURL request and get the response
    $response = curl_exec($ch);

    // Check if cURL executed successfully
    if (curl_errno($ch)) {
        // Return error message if the cURL request failed
        echo json_encode(['error' => 'Failed to generate image, try again later.']);
        exit;
    }

    // Close cURL session
    curl_close($ch);

    // Decode and return the response as JSON
    $response_data = json_decode($response, true);

    if (isset($response_data['output_url'])) {
        // Return the image URL from the response
        echo json_encode(['image_url' => $response_data['output_url']]);
    } else {
        // Return error if no image URL is found in the response
        echo json_encode(['error' => 'Unable to generate image.']);
    }

    exit;
}

// Return an error message if POST data is missing
echo json_encode(['error' => 'Invalid request.']);
?>
