<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $apiKey = "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"; // Replace with your OpenRouter API key
    $apiKey="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"; //
    $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

    $userQuery = isset($_POST['query']) ? trim($_POST['query']) : '';

    if (!empty($userQuery)) {
        $data = array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array("role" => "system", "content" => "You are a helpful assistant."),
                array("role" => "user", "content" => $userQuery)
            )
        );

        $postData = json_encode($data);

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey",
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo json_encode(array("error" => curl_error($ch)));
            exit;
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['choices'][0]['message']['content'])) {
            echo json_encode(array("answer" => $responseData['choices'][0]['message']['content']));
        } else {
            echo json_encode(array("error" => "Invalid API response"));
        }
    } else {
        echo json_encode(array("error" => "Query is required"));
    }

    exit;
}
?>
