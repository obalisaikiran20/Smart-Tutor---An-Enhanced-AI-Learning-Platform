<?php include('header.php'); ?>
<br><br>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>CodeCraft - Problem 8: Swapping of Numbers</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs/loader.min.js"></script>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: calc(100vh - 100px);
      margin-top: 80px;
      background-color: #f4f4f4;
      gap: 20px;
      padding: 20px;
      box-sizing: border-box;
    }

    .question-panel h2 {
  font-size: 20px;
  color: darkgreen;
}

.question-panel p {
  font-size: 16px;
  color: #333;
  line-height: 1.5;
}

.question-panel pre {
  background: #f2f2f2;
  padding: 10px;
  border-radius: 8px;
}


    .editor-panel {
      width: 70%;
      padding: 20px;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      max-height: calc(100vh - 140px);
      overflow-y: auto;
    }

    .question-panel h2 {
      font-size: 22px;
      color: #228b22;
    }

    .question-panel p {
      font-size: 16px;
      color: #333;
      line-height: 1.5;
    }

    .question-panel pre {
      background: #f2f2f2;
      padding: 10px;
      border-radius: 8px;
      color: #000;
    }

    #editor {
      height: 300px;
      border: 1px solid #ccc;
      border-radius: 10px;
      margin-bottom: 10px;
    }

    select, button, #input {
      margin: 10px 0;
      padding: 10px;
      font-size: 16px;
      border-radius: 8px;
      border: 1px solid #ccc;
      width: 100%;
      box-sizing: border-box;
    }

    button {
      background-color: orangered;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #e63900;
    }

    #output {
      background: #000;
      color: lime;
      padding: 10px;
      font-family: monospace;
      min-height: 100px;
      border-radius: 8px;
      margin-top: 10px;
      white-space: pre-wrap;
    }
  </style>
</head>
<body>

<div class="question-panel">
  <h2>🔁 Problem 4: Swapping of Numbers</h2>

  <div>
    <p><strong>📝 Description:</strong></p>
    <p>Write a program to swap two numbers and display the result.</p>
  </div>

  <div>
    <p><strong>📥 Input:</strong></p>
    <p>Two integers (space-separated)</p>
  </div>

  <div>
    <p><strong>📤 Output:</strong></p>
    <p>The values of the two numbers after swapping</p>
  </div>

  <div>
    <p><strong>📌 Example:</strong></p>
    <pre>
Input: 5 10
Output: 10 5
    </pre>
  </div>

  <div>
    <p><strong>🧪 Test Cases:</strong></p>
    <pre id="test-cases">5 10</pre>
  </div>
</div>

<div class="editor-panel">
  <label for="language">Language:</label>
  <select id="language">
    <option value="50">C</option>
    <option value="54">C++</option>
    <option value="62">Java</option>
    <option value="71" selected>Python</option>
    <option value="78">Kotlin</option>
    <option value="63">JavaScript</option>
  </select>

  <div id="editor"><h4 style="color:#FFFAFA;text-align:center; margin-top:25px; padding-top:7px;margin-left:300px; border-radius:8px; background-color:#355e3b; height:30px; width: 350px;">
      Let's Dive into New Challenges!
    </h4><br><br></div>
  <textarea id="input" rows="3"></textarea>
   <br><br><br><br>
  <button onclick="runCode()">Run Code ▶</button>
  <button onclick="submitCode()">Submit Code ✅</button>

  <div id="output">Your output will appear here...</div>
</div>

<script>
  let editor;
  require.config({ paths: { vs: "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs" } });
  require(["vs/editor/editor.main"], function () {
    editor = monaco.editor.create(document.getElementById("editor"), {
      value: "# Write your code here",
      language: "python",
      theme: "vs-dark",
      automaticLayout: true
    });
  });

  const languageMap = {
    "50": "c",
    "54": "cpp",
    "62": "java",
    "71": "python",
    "78": "kotlin",
    "63": "javascript"
  };

  document.getElementById("language").addEventListener("change", function () {
    const lang = languageMap[this.value];
    monaco.editor.setModelLanguage(editor.getModel(), lang);
  });

  window.onload = () => {
    document.getElementById("input").value = document.getElementById("test-cases").innerText.trim();
  };

  function runCode() {
    const code = editor.getValue();
    const input = document.getElementById("input").value;
    const language = document.getElementById("language").value;
    const outputDiv = document.getElementById("output");

    outputDiv.innerHTML = "⏳ Running your code...";

    fetch("https://judge0-ce.p.rapidapi.com/submissions?base64_encoded=false&wait=true", {
      method: "POST",
      headers: {
        "content-type": "application/json",
        "X-RapidAPI-Key": "YOUR_RAPIDAPI_KEY",
        "X-RapidAPI-Host": "judge0-ce.p.rapidapi.com"
      },
      body: JSON.stringify({
        source_code: code,
        language_id: language,
        stdin: input
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.stdout) {
        outputDiv.innerHTML = `<pre>${data.stdout}</pre>`;
      } else if (data.stderr) {
        outputDiv.innerHTML = `<pre style="color:red;">${data.stderr}</pre>`;
      } else if (data.compile_output) {
        outputDiv.innerHTML = `<pre style="color:red;">${data.compile_output}</pre>`;
      } else {
        outputDiv.innerHTML = `<pre style="color:red;">Error: ${data.message}</pre>`;
      }
    })
    .catch(err => {
      outputDiv.innerHTML = `<pre style="color:red;">Error: ${err}</pre>`;
    });
  }

  function submitCode() {
    const code = editor.getValue();
    const input = document.getElementById("input").value;
    const language = document.getElementById("language").value;
    const outputDiv = document.getElementById("output");

    outputDiv.innerHTML = "⏳ Submitting your code...";

    fetch("https://judge0-ce.p.rapidapi.com/submissions?base64_encoded=false&wait=true", {
      method: "POST",
      headers: {
        "content-type": "application/json",
        "X-RapidAPI-Key": "d3f0a35dbcmshe76e9af8100d392p175a66jsn00d23410b1acd3f0a35dbcmshe76e9af8100d392p175a66jsn00d23410b1acs",
        "X-RapidAPI-Host": "judge0-ce.p.rapidapi.com"
      },
      body: JSON.stringify({
        source_code: code,
        language_id: language,
        stdin: input
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.stdout && data.stdout.trim() === "10 5") {
        outputDiv.innerHTML = `<pre style="color:green;">Happy Coding Dear You Have Achieved a Milestone 🎉</pre>`;
      } else {
        outputDiv.innerHTML = `<pre style="color:red;">Better Luck Next Time ❌</pre>`;
      }
    })
    .catch(err => {
      outputDiv.innerHTML = `<pre style="color:red;">Error: ${err}</pre>`;
    });
  }
</script>

</body>
</html>
