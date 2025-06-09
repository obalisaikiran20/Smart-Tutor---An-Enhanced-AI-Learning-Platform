<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Tutor - Assessment Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #e3f2fd;
        }
        .header {
            background-color: navy;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            height: 50px;
            padding-top: 17px;
            font-weight: bold;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000; /* Ensures header stays on top */
        }
        .back-button {
            position: absolute;
            left: 20px;
            background: orangered;
            color: white;
            padding: 10px;
            width: 80px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .chat-container {
            max-width: 700px;
            margin: 100px auto 0;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-top: 80px;
        }
        .chat-box {
            max-height: 500px;
            overflow-y: auto;
            /* margin: 20px; */
            padding: 15px;
            margin-top: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .input-container {
            display: flex;
            justify-content: space-between;
        }
        .input-container input {
            width: 80%;
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
        }
        .input-container button:hover {
            background-color: #0056b3;
        }
        .loading {
            font-weight: bold;
            color: #ff4500;
            margin: 15px;
            text-align: center;
        }
        .bot-message {
            background-color: #e1f5fe;
            padding: 12px;
            border-radius: 10px;
            font-family: 'Times New Roman', serif;
            text-align: justify;
            margin: 15px;
            font-size: 16px;
            border-left: 5px solid #2196F3;
            position: relative;
        }
        .bot-message strong {
            display: block;
            text-align: center;
            color: orangered;
        }
        .bot-message::after {
            content: "Smart Tutor";
            position: absolute;
            bottom: 5px;
            right: 10px;
            font-size: 12px;
            color: rgba(0, 0, 0, 0.5);
        }
        .print-button {
            display: none;
            margin-top: 10px;
            padding: 8px 15px;
            border: none;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .print-button:hover {
            background-color: #218838;
        }
        .question {
            font-weight: bold;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #e1f5fe;
            border: 1px solid #2196F3;
        }
        .answer {
            display: none;
            margin-top: 5px;
            padding: 8px;
            border: 1px solid #2196F3;
            background-color: #E1F5FE;
            border-radius: 5px;
            text-align: justify;
        }
        .view-answer {
            margin-top: 5px;
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .view-answer:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="header">
    <button class="back-button" onclick="window.location.href='canti.php';">Back</button>Smart Tutor</div><br><br><br><br><br><br>
<div class="chat-container">
    <h2>Smart Tutor - Assessment Generator</h2>
    <div id="chat-box" class="chat-box"></div>
    <button id="print-button" class="print-button" onclick="printContent()">Print Notes</button><br><br>
    <div class="input-container">
        <input type="text" id="user-query" placeholder="Enter assessment topic..." onkeypress="handleKeyPress(event)">
        <button onclick="sendQuery()">Generate</button>
    </div>
</div>

<script>
    function sendQuery() {
        var userQuery = document.getElementById('user-query').value.trim();
        if (userQuery === "") return;

        document.getElementById('print-button').style.display = 'none'; 
        addMessage('<strong>Topic:</strong> ' + userQuery, 'bot-message');
        showLoading();

        fetch('ass_backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'query=' + encodeURIComponent(userQuery),
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.error) {
                addMessage('Error: ' + data.error, 'bot-message');
            } else {
                addMessage('<strong>Assessment Questions</strong>', 'bot-message');
                data.questions.forEach((q, index) => {
                    addMessage('<div class="question">Q' + (index + 1) + ': ' + q.question + '</div>', 'bot-message');
                    var answerHtml = `<div class="answer" id="answer-${index}">${q.answer}</div>`;
                    var buttonHtml = `<button class="view-answer" onclick="toggleAnswer(${index})">View Answer</button>`;
                    addMessage(buttonHtml + answerHtml, 'bot-message');
                });
                document.getElementById('print-button').style.display = 'inline-block'; 
            }
        })
        .catch(() => {
            hideLoading();
            addMessage('Error: Something went wrong!', 'bot-message');
        });

        document.getElementById('user-query').value = '';
    }

    function addMessage(message, className) {
        var chatBox = document.getElementById('chat-box');
        var messageDiv = document.createElement('div');
        messageDiv.innerHTML = message;
        messageDiv.classList.add(className);
        chatBox.appendChild(messageDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function showLoading() {
        var chatBox = document.getElementById('chat-box');
        var loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading';
        loadingDiv.className = 'loading';
        loadingDiv.textContent = 'Smart Tutor is working on your request...';
        chatBox.appendChild(loadingDiv);
    }

    function hideLoading() {
        var loadingDiv = document.getElementById('loading');
        if (loadingDiv) loadingDiv.remove();
    }

    function handleKeyPress(event) {
        if (event.key === "Enter") {
            sendQuery();
        }
    }

    function toggleAnswer(index) {
        var answerDiv = document.getElementById('answer-' + index);
        answerDiv.style.display = (answerDiv.style.display === 'none' || answerDiv.style.display === '') ? 'block' : 'none';
    }

    function printContent() {
        var chatBox = document.getElementById('chat-box').innerHTML;
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Smart Tutor - Print Notes</title></head><body>');
        printWindow.document.write(chatBox);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
</script>

</body>
</html>