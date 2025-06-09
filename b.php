<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StudyBot - Smart Tutor</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding-top: 80px;
    }

    .chat-container {
      max-width: 800px;
      margin: auto;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .messages {
      max-height: 450px;
      overflow-y: auto;
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 8px;
      background: #fafafa;
      margin-bottom: 15px;
    }

    .message {
      margin-bottom: 10px;
      padding: 8px 12px;
      border-radius: 8px;
    }

    .user {
      background: #d1e7ff;
      text-align: right;
    }

    .bot {
      background: #e2f0cb;
      text-align: left;
    }

    .input-area {
      display: flex;
      gap: 10px;
    }

    input[type="text"] {
      flex: 1;
      padding: 10px;
      font-size: 16px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    button {
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 8px;
      border: none;
      background: #007bff;
      color: white;
      cursor: pointer;
    }

    button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

<div class="chat-container">
  <h2>📝 Assessment Generator</h2>
  <div class="messages" id="chat"></div>
  <div class="input-area">
    <input type="text" id="userInput" placeholder="Ask something to generate assessment questions..." />
    <button onclick="sendMessage()">Send</button>
  </div>
</div>

<script>
  function sendMessage() {
    const userText = document.getElementById("userInput").value;
    if (!userText) return;

    const chatBox = document.getElementById("chat");
    chatBox.innerHTML += `<div class='message user'>${userText}</div>`;
    document.getElementById("userInput").value = "";

    fetch("assessment_generator.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "prompt=" + encodeURIComponent(userText)
    })
    .then(res => res.text())
    .then(data => {
      chatBox.innerHTML += `<div class='message bot'>${data}</div>`;
      chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(err => {
      chatBox.innerHTML += `<div class='message bot'>⚠️ Error: ${err}</div>`;
    });
  }
</script>

</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["prompt"])) {
    $prompt = $_POST["prompt"];

    $apiKey = "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";

    $data = [
        "model" => "openai/gpt-3.5-turbo",
        "messages" => [
            [
                "role" => "system",
                "content" => "You are a smart tutor AI. Based on the user's input topic, generate a mock test in clean HTML format with the following exact format and rules:

✅ Output:
- Minimum 10 questions.
- Do not categorize questions by marks (e.g., 1 mark, 2 mark, etc.).

✅ Each question and answer must be structured like this:
<div class='question' style='margin-bottom: 30px;'>
  <strong>Qn. [Question Text]</strong>
  <h3 style='margin-top: 15px; margin-bottom: 10px;'>Answer:</h3>
  <div class='answer' style='text-align: justify; line-height: 1.7;'>
    <p>Paragraph 1 (at least 7 full lines)</p>
    <p>Paragraph 2 (at least 7 full lines)</p>
    <p>Paragraph 3 (at least 7 full lines)</p>
    <ul>
      <li>Bullet point 1</li>
      ...
      <li>Bullet point 10</li>
    </ul>
  </div>
</div>

⚠️ Rules:
- Do NOT include any 'View Answer' buttons.
- Add margin between question and answer section.
- Each paragraph must be at least 7 lines long.
- Each answer must contain exactly 3 well-explained justified paragraphs.
- Include exactly 10 bullet points.
- Use clean HTML with no extra tags or styling.
- No extra explanations before or after the HTML content.
- Language should be formal and academic."
            ],
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData["choices"][0]["message"]["content"])) {
        echo "<div style='font-family: Arial, sans-serif; padding: 25px; background: #ffffff; border-radius: 12px; border: 1px solid #ddd; line-height: 1.7;'>";
        echo "<h2 style='text-align: center; font-weight: bold; margin-top: 10px; margin-bottom: 30px;'>Assessment Questions</h2>";
        echo $responseData["choices"][0]["message"]["content"];
        echo "</div>";
    } else {
        echo "<strong>❌ Error:</strong><br><pre>" . htmlspecialchars($response) . "</pre>";
    }
}
?>
