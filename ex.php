<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Tutor - External Links</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #e3f2fd;
        }
        .header {
            width: 100%;
            background: navy;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            align-items: center;
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
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .chat-box {
            max-height: 500px;
            overflow-y: auto;
            margin: 20px;
            padding: 15px;
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
            border-radius: px;
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
        .link-btn {
            display: block;
            padding: 10px;
            margin: 5px 0;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            font-size: 16px;
        }
        .link-btn:hover {
            background-color: #1976D2;
        }
        .print-button {
            display: none;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff9800;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .print-button:hover {
            background-color: #e68900;
        }
    </style>
</head>
<body>
<div class="header">
    <button class="back-button" onclick="window.location.href='canti.php';">Back</button>Smart Tutor</div><br><br><br><br><br><br>

<div class="chat-container">
    <h2>Smart Tutor - External Links</h2>
    <div id="chat-box" class="chat-box"></div>
    <div class="input-container">
        <input type="text" id="user-query" placeholder="Enter a topic..." onkeypress="handleKeyPress(event)">
        <button onclick="sendQuery()">Search</button>
    </div>
    <button id="print-button" class="print-button" onclick="printContent()">Print Links</button>
</div>

<script>
    function sendQuery() {
        var userQuery = document.getElementById('user-query').value.trim();
        if (userQuery === "") return;

        document.getElementById('print-button').style.display = 'none'; 
        addMessage('<strong>Topic:</strong> ' + userQuery, 'bot-message');
        showLoading();

        fetch('exb.php?query=' + encodeURIComponent(userQuery))
        .then(response => response.text())
        .then(data => {
            hideLoading();
            document.getElementById('chat-box').innerHTML += data;
            document.getElementById('print-button').style.display = 'block'; 
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
        loadingDiv.textContent = 'Smart Tutor is fetching resources...';
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

    function printContent() {
        var chatBox = document.getElementById('chat-box').innerHTML;
        var printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Smart Tutor - Print Links</title></head><body>');
        printWindow.document.write(chatBox);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
</script>

</body>
</html>
