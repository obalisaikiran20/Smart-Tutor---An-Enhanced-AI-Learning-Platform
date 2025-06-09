<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CodeCraft - Question Progress</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #e3f2fd;
      color: #333;
    }

    .container {
      max-width: 1000px;
      margin: 120px auto;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 10px;
    }

    .progress-container {
      background-color: #ddd;
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 30px;
    }

    .progress-bar {
      height: 30px;
      background-color: #4CAF50;
      width: 0%;
      line-height: 30px;
      color: white;
      text-align: center;
      transition: width 0.4s ease-in-out;
    }

    .question-box {
      background-color: #fff;
      border-radius: 10px;
      padding: 15px 20px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
    }

    .question-box:hover {
      transform: translateY(-3px);
    }

    .question-box label {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 18px;
      color: #333;
    }

    .question-box input[type="checkbox"] {
      margin-right: 10px;
    }

    .question-box a {
      padding: 8px 16px;
      background-color: orangered;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      transition: background 0.3s ease;
    }

    .question-box a:hover {
      background-color: #e63900;
    }

    .message {
      font-size: 18px;
      color: red;
      font-weight: bold;
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🚀 Progress</h2>
    <div class="progress-container">
      <div class="progress-bar" id="progress-bar">0%</div>
    </div>

    <div id="questions-list">
      <!-- Questions will be dynamically injected here -->
    </div>

    <div id="message" class="message"></div>
  </div>

  <script>
    const questions = [
      "Even or Odd",
      "Check Prime",
      "Factorial of a Number",
      "Swap Two Numbers",
      "Check if Array is Sorted",
      "String Palindrome",
      "Reverse a String",
      "Find an Element in Array",
      "Sum of Array",
      "Frequency of Element in Array",
      "Two Sum",
      "Find Duplicate Elements",
      "Find Minimum in Array",
      "Find Maximum in Array",
      "Number Palindrome",
      "Reverse The Array",
      "Reverse The Number",
      "Sum of Numbers",
      "Unique Number",
      "Factors of Number"
    ];

    const links = [
      "evenodd.php", "prime.php", "factorial.php", "swap.php", "arraysort.php",
      "strpali.php", "reversestring.php", "find.php", "arraysum.php",
      "frec.php", "twosum.php", "duplicate.php", "mini.php",
      "maximum.php", "numpali.php", "reversearray.php", "reverseint.php",
      "q1.php", "unique.php", "factors.php"
    ];

    const container = document.getElementById("questions-list");

    questions.forEach((question, index) => {
      const box = document.createElement("div");
      box.className = "question-box";
      box.innerHTML = `
        <label><input type="checkbox" onchange="updateProgress()"> ${index + 1}. ${question}</label>
        <a href="${links[index]}">Solve ➡️</a>
      `;
      container.appendChild(box);
    });

    function updateProgress() {
      const checkboxes = document.querySelectorAll("input[type='checkbox']");
      const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
      const percent = Math.round((checked / checkboxes.length) * 100);
      const bar = document.getElementById("progress-bar");
      bar.style.width = percent + "%";
      bar.innerText = percent + "%";
      
      if (percent === 100) {
        document.getElementById('message').textContent = "🎉 You have completed all the tasks!";
      } else {
        document.getElementById('message').textContent = "";
      }
    }
  </script>
</body>
</html>
