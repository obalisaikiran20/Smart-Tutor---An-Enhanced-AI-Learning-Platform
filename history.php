<?php
// Database connection
$dbHost = 'localhost'; // Change if needed
$dbUser = 'root'; // Your database username
$dbPassword = ''; // Your database password
$dbName = 'smart_tutor'; // The database name

$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch chat history from the database
$sql = "SELECT user_query, chatbot_response, created_at FROM lecture_notes ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Tutor - Chat History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 20px;
        }
        .header {
            background-color: navy;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
        }
        .chat-history-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .chat-entry {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
        }
        .chat-entry strong {
            display: block;
            margin-bottom: 5px;
            color: #007bff;
        }
        .chat-entry p {
            font-size: 16px;
            color: #333;
        }
        .timestamp {
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        Smart Tutor - Chat History
    </div>

    <div class="chat-history-container">
        <h2>Lecture Notes History</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="chat-entry">';
                echo '<strong>User Query:</strong>';
                echo '<p>' . htmlspecialchars($row['user_query']) . '</p>';
                echo '<strong>Chatbot Response:</strong>';
                echo '<p>' . htmlspecialchars($row['chatbot_response']) . '</p>';
                echo '<p class="timestamp">Asked on: ' . $row['created_at'] . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No chat history found.</p>';
        }
        ?>
    </div>

</body>
</html>
<?php
$conn->close();
?>
