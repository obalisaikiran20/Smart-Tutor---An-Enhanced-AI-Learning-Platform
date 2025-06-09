<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CodeCraft - Fullscreen IDE</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs/loader.min.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e3f2fd;
      padding-top: 80px; /* for header */
    }

    .container {
      padding: 20px;
      max-width: 100%;
    }

    .controls {
      display: flex;
      gap: 10px;
      margin-bottom: 10px;
      align-items: center;
    }

    select, button {
      padding: 10px 15px;
      font-size: 16px;
      border-radius: 8px;
      border: 1px solid #ccc;
      cursor: pointer;
    }

    button {
      background-color: #ff5722;
      color: #fff;
      border: none;
    }

    button:hover {
      background-color: #e64a19;
    }

    #editor {
      height: 400px;
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 15px;
    }

    textarea, #output {
      width: 100%;
      padding: 10px;
      font-size: 15px;
      margin-bottom: 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
    }

    #output {
      background: #000;
      color: #00ff00;
      font-family: monospace;
      min-height: 120px;
      white-space: pre-wrap;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="controls">
    <label for="language">Language:</label>
    <select id="language">
      <option value="50">C</option>
      <option value="54">C++</option>
      <option value="62">Java</option>
      <option value="71" selected>Python</option>
      <option value="63">JavaScript</option>
    </select>
    <button onclick="runCode()">▶ Run</button>
  </div>

  <div id="editor"># Write your code here</div><br><br>

  <label for="input">📥 Input:</label>
  <textarea id="input" rows="3" placeholder="Enter your input here..."></textarea>

  <label for="output">📤 Output:</label>
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
    "63": "javascript"
  };

  document.getElementById("language").addEventListener("change", function () {
    const lang = languageMap[this.value];
    monaco.editor.setModelLanguage(editor.getModel(), lang);
  });

  function runCode() {
    const code = editor.getValue();
    const input = document.getElementById("input").value;
    const language = document.getElementById("language").value;
    const outputDiv = document.getElementById("output");
    outputDiv.textContent = "⏳ Running your code...";

    fetch("https://judge0-ce.p.rapidapi.com/submissions?base64_encoded=false&wait=true", {
      method: "POST",
      headers: {
        "content-type": "application/json",
        "X-RapidAPI-Key": "d3f0a35dbcmshe76e9af8100d392p175a66jsn00d23410b1ac",
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
        outputDiv.innerText = data.stdout;
      } else if (data.stderr) {
        outputDiv.innerText = "Error:\n" + data.stderr;
      } else if (data.compile_output) {
        outputDiv.innerText = "Compilation Error:\n" + data.compile_output;
      } else {
        outputDiv.innerText = "Unknown error occurred.";
      }
    })
    .catch(err => {
      outputDiv.innerText = "Request Error:\n" + err;
    });
  }
</script>
</body>
</html>
