<?php
$youtube_api_key = "AIzaSyB1HKXf0agMO69MnHHSXmQkYCTWpkjwTa0";
$google_cse_id = "6209db32c85af47aa";
$query = isset($_GET['query']) ? trim($_GET['query']) : "";

if (!$query) {
    echo "No query provided.";
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "smart_tutor");
if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}

// Fetch YouTube Links
$youtube_url = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=10&q=" . urlencode($query) . "&type=video&key=$youtube_api_key";
$youtube_response = file_get_contents($youtube_url);
$youtube_data = json_decode($youtube_response, true);

echo "<h3>YouTube Links</h3>";
if (!empty($youtube_data['items'])) {
    foreach ($youtube_data['items'] as $item) {
        if (isset($item['id']['videoId'])) {
            $video_id = $item['id']['videoId'];
            $video_title = htmlspecialchars($item['snippet']['title']);
            $video_url = "https://www.youtube.com/watch?v=$video_id";

            echo "<a href='$video_url' class='link-btn' target='_blank'>$video_title</a>";

            // Insert into DB
            $stmt = $conn->prepare("INSERT INTO external_resources_history (query, source_type, title, url) VALUES (?, 'YouTube', ?, ?)");
            $stmt->bind_param("sss", $query, $video_title, $video_url);
            $stmt->execute();
            $stmt->close();
        }
    }
} else {
    echo "No YouTube results found.";
}

// Fetch Website Links
$google_search_url = "https://www.googleapis.com/customsearch/v1?q=" . urlencode($query) . "&cx=$google_cse_id&key=$youtube_api_key";
$google_response = file_get_contents($google_search_url);
$google_data = json_decode($google_response, true);

echo "<h3>Website Links</h3>";
if (!empty($google_data['items'])) {
    foreach ($google_data['items'] as $result) {
        $site_title = htmlspecialchars($result['title']);
        $site_url = $result['link'];

        echo "<a href='$site_url' class='link-btn' target='_blank'>$site_title</a>";

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO external_resources_history (query, source_type, title, url) VALUES (?, 'Website', ?, ?)");
        $stmt->bind_param("sss", $query, $site_title, $site_url);
        $stmt->execute();
        $stmt->close();
    }
} else {
    echo "No website results found.";
}

$conn->close();
?>
