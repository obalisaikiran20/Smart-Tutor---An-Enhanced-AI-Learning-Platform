<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = "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";
    $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

    $userQuery = isset($_POST['query']) ? trim($_POST['query']) : '';

    if (!empty($userQuery)) {
        $prompt = "Generate a quiz with 5 multiple-choice questions based on the topic '$userQuery'. " .
            "Each question should have 4 answer choices labeled A, B, C, and D, with only one correct answer. " .
            "Also provide a brief explanation for each correct answer. " .
            "Provide the quiz in this JSON format:\n" .
            "{ \"questions\": [ " .
            "{ \"question\": \"Question text\", \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"], \"answer\": \"A\", \"explanation\": \"Explanation text\" }, " .
            "{ \"question\": \"Question text\", \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"], \"answer\": \"B\", \"explanation\": \"Explanation text\" } " .
            "] }";

        $data = array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array("role" => "system", "content" => "You are an AI that generates structured JSON quizzes."),
                array("role" => "user", "content" => $prompt)
            ),
            'max_tokens' => 500
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
            echo json_encode(["error" => curl_error($ch)]);
            exit;
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['choices'][0]['message']['content'])) {
            $quizJson = $responseData['choices'][0]['message']['content'];

            // Decode the JSON content returned by the model
            $quizData = json_decode($quizJson, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($quizData['questions'])) {
                // ✅ Connect to database
                $conn = new mysqli('localhost', 'root', '', 'smart_tutor');
                if ($conn->connect_error) {
                    die(json_encode(["error" => "DB connection failed: " . $conn->connect_error]));
                }

                $stmt = $conn->prepare("INSERT INTO quiz_history 
                    (topic, question, option_a, option_b, option_c, option_d, correct_answer, explanation)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                foreach ($quizData['questions'] as $q) {
                    $question = $q['question'] ?? '';
                    $options = $q['options'] ?? ["", "", "", ""];
                    $answer = $q['answer'] ?? '';
                    $explanation = $q['explanation'] ?? '';

                    $stmt->bind_param(
                        "ssssssss",
                        $userQuery,
                        $question,
                        $options[0],
                        $options[1],
                        $options[2],
                        $options[3],
                        $answer,
                        $explanation
                    );
                    $stmt->execute();
                }

                $stmt->close();
                $conn->close();

                // Return raw quiz JSON back to frontend for rendering
                echo $quizJson;

            } else {
                echo json_encode(["error" => "Failed to decode quiz JSON."]);
            }
        } else {
            echo json_encode(["error" => "Invalid API response."]);
        }
    } else {
        echo json_encode(["error" => "Query is required."]);
    }
    exit;
}
?>
