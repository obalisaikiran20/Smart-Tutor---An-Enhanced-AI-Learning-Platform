<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Smart Tutor - Quiz Generator</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #e3f2fd;
      text-align: justify;
    }

    .container {
      max-width: 800px;
      margin: 130px auto 20px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .input-container {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .input-container input {
      flex: 1;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ddd;
    }

    .input-container button {
      padding: 10px 15px;
      border-radius: 5px;
      border: none;
      background-color: #007bff;
      color: white;
      cursor: pointer;
      font-size: 16px;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
    }

    .input-container button:hover {
      background-color: #0056b3;
    }

    .bot-message {
      background-color: #f0f0f0;
      color: #333;
      border-left: 5px solid #9e9e9e;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 10px;
      font-weight: bold;
    }

    .question {
      padding: 15px;
      border-radius: 5px;
      margin: 15px 0;
      font-weight: bold;
      background: #d4edda;
      border-left: 5px solid #155724;
    }

    .answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.5s ease;
      margin-top: 10px;
      padding: 0 10px;
      background: #cce5ff;
      border-left: 5px solid #007bff;
      border-radius: 5px;
    }

    .answer.open {
      padding: 10px;
      max-height: 500px;
    }

    .view-answer {
      display: none;
      margin-top: 8px;
      padding: 6px 12px;
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }

    .view-answer:hover {
      background-color: #0056b3;
    }

    .submit-btn, .print-btn {
      padding: 12px;
      border: none;
      color: white;
      cursor: pointer;
      font-size: 16px;
      border-radius: 5px;
      margin-top: 25px;
      display: block;
      width: 100%;
      text-align: center;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
    }

    .submit-btn {
      background-color: #ff9800;
    }

    .submit-btn:hover {
      background-color: #e68900;
    }

    .print-btn {
      background-color: #4CAF50;
      display: none;
    }

    .print-btn:hover {
      background-color: #45a049;
    }

    .accuracy {
      display: none;
      font-size: 18px;
      font-weight: bold;
      color: #28a745;
      margin-top: 20px;
      text-align: center;
    }

    .loading {
      font-weight: bold;
      color: #ff4500;
      text-align: center;
      margin-top: 20px;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    @media print {
      body * {
        visibility: hidden;
      }

      .container, .container * {
        visibility: visible;
      }

      .input-container,
      .submit-btn,
      .print-btn,
      .view-answer {
        display: none !important;
      }

      .answer {
        max-height: none !important;
        display: block !important;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Smart Tutor - Quiz Generator</h2>

    <div class="input-container">
      <input type="text" id="topic" placeholder="Enter quiz topic...">
      <button onclick="generateQuiz()">Generate Quiz</button>
    </div>

    <div id="quiz-container"></div>

    <p id="accuracy" class="accuracy"></p>
    <button id="print-btn" class="print-btn" onclick="window.print()">Print Quiz</button>
  </div>

  <script>
    let correctAnswers = [];

    function generateQuiz() {
      const topic = document.getElementById('topic').value.trim();
      if (!topic) return;

      const quizContainer = document.getElementById('quiz-container');
      quizContainer.innerHTML = '<p class="loading">Generating quiz, please wait...</p>';

      fetch('quizz_backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'query=' + encodeURIComponent(topic)
      })
      .then(response => response.json())
      .then(data => {
        quizContainer.innerHTML = `<div class="bot-message">Quiz on "${topic}"</div>`;
        let quizHTML = '<form id="quiz-form">';
        correctAnswers = data.questions.map(q => q.answer);
        data.questions.forEach((q, index) => {
          quizHTML += `
            <div class='question'>
              ${q.question}<br>
              <input type="radio" name="q${index}" value="A"> A) ${q.options[0]}<br>
              <input type="radio" name="q${index}" value="B"> B) ${q.options[1]}<br>
              <input type="radio" name="q${index}" value="C"> C) ${q.options[2]}<br>
              <input type="radio" name="q${index}" value="D"> D) ${q.options[3]}<br>
              <button type="button" class="view-answer" id="view-answer-${index}" onclick="toggleAnswer(${index})">View Answer</button>
              <div class='answer' id='answer-${index}'>
                <strong>Correct Answer:</strong> ${q.answer}<br>
                <strong>Explanation:</strong> ${q.explanation}
              </div>
            </div>`;
        });
        quizHTML += `<button type="button" class="submit-btn" onclick="submitQuiz()">Submit</button>`;
        quizContainer.innerHTML += quizHTML;
      });
    }

    function toggleAnswer(index) {
      const answer = document.getElementById(`answer-${index}`);
      answer.classList.toggle("open");
    }

    function submitQuiz() {
      const totalQuestions = correctAnswers.length;
      const userAnswers = document.querySelectorAll("input[type='radio']:checked");

      if (userAnswers.length < totalQuestions) {
        alert("Please answer all questions before submitting.");
        return;
      }

      let score = 0;
      userAnswers.forEach((answer, index) => {
        if (answer.value === correctAnswers[index]) {
          score++;
        }
        document.getElementById(`view-answer-${index}`).style.display = 'inline-block';
      });

      const accuracy = (score / totalQuestions) * 100;
      document.getElementById("accuracy").innerHTML = `Your Accuracy: ${accuracy.toFixed(2)}%`;
      document.getElementById("accuracy").style.display = "block";
      document.getElementById("print-btn").style.display = "block";
    }
  </script>
</body>
</html>
