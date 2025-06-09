<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $apiKey = "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";
    $apiKey="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";
    $apiUrl = "https://openrouter.ai/api/v1/chat/completions";

    $userQuery = isset($_POST['query']) ? trim($_POST['query']) : '';

    if (!empty($userQuery)) {
        $prompt = "Generate an assessment with 5 questions based on the topic '$userQuery'. " .
                  "Each answer should be at least 10 lines long and should be properly formatted for readability." .
                  "Provide a structured format with 'Q1:', 'Answer:' for each question.";

        $data = array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array("role" => "system", "content" => "You are a helpful assistant that generates assessments."),
                array("role" => "user", "content" => $prompt)
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
            $assessmentText = $responseData['choices'][0]['message']['content'];

            // Split the response by 'Q1:', 'Q2:', etc.
            $questionBlocks = preg_split('/(Q\d+:)/', $assessmentText, -1, PREG_SPLIT_DELIM_CAPTURE);
            
            $questions = [];
            for ($i = 1; $i < count($questionBlocks); $i += 2) {
                $questionText = trim(strip_tags($questionBlocks[$i] . $questionBlocks[$i + 1]));

                // Extract answer by searching for "Answer:"
                $answerParts = explode("Answer:", $questionText);
                $question = trim($answerParts[0]);
                $answer = isset($answerParts[1]) ? nl2br(trim($answerParts[1])) : "Answer not provided.";

                $questions[] = [
                    "question" => "<p class='question'>" . htmlspecialchars($question) . "</p>",
                    "answer" => "<p style='text-align:justify;'>" . $answer . "</p>"
                ];
            }

            // If no questions were found, return an error
            if (empty($questions)) {
                echo json_encode(["error" => "Failed to extract questions. Please try again."]);
            } else {
                echo json_encode(["questions" => $questions]);
            }
        } else {
            echo json_encode(["error" => "Invalid API response"]);
        }
    } else {
        echo json_encode(["error" => "Query is required"]);
    }
    exit;
}
?>
