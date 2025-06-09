z<?php
include 'config.php';
include 'header.php';
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// $username = $_SESSION["user"];
// $user = $conn->query("SELECT id, username FROM users WHERE username = '$username'")->fetch_assoc();
// $userId = $user["id"];

// $profileQuery = $conn->query("SELECT * FROM user_profiles WHERE user_id = '$userId'");
// if ($profileQuery->num_rows > 0) {
//     $userData = $profileQuery->fetch_assoc();
// } else {
//     $conn->query("INSERT INTO user_profiles (user_id) VALUES ('$userId')");
//     $userData = $conn->query("SELECT * FROM user_profiles WHERE user_id = '$userId'")->fetch_assoc();
// }

// $profileUpdated = false;
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["first_name"])) {
//     $first_name = $_POST["first_name"];
//     $last_name = $_POST["last_name"];
//     $gender = $_POST["gender"];
//     $dob = $_POST["dob"];
//     $mobile = $_POST["mobile"];
//     $grade = $_POST["grade"];
//     $branch = $_POST["branch"];
//     $institution = $_POST["institution"];
//     $graduation_year = $_POST["graduation_year"];
//     $courses = $_POST["courses"];

//     $conn->query("UPDATE user_profiles SET 
//         first_name='$first_name', last_name='$last_name', gender='$gender', dob='$dob', 
//         mobile='$mobile', grade='$grade', branch='$branch', institution='$institution', 
//         graduation_year='$graduation_year', courses='$courses'
//         WHERE user_id='$userId'");

//     $profileUpdated = true;
//     echo '<script>
//         setTimeout(function() {
//             window.location.href = "canti.php";
//         }, 2000);
$username = $_SESSION["user"];
$user = $conn->query("SELECT id, username FROM users WHERE username = '$username'")->fetch_assoc();
$userId = $user["id"];

// Fetch or create profile record
$profileQuery = $conn->query("SELECT * FROM user_profiles WHERE user_id = '$userId'");
if ($profileQuery->num_rows > 0) {
    $userData = $profileQuery->fetch_assoc();
} else {
    $conn->query("INSERT INTO user_profiles (user_id) VALUES ('$userId')");
    $userData = $conn->query("SELECT * FROM user_profiles WHERE user_id = '$userId'")->fetch_assoc();
}

// Check if form is submitted and if 'edit_mode' is on
$profileUpdated = false;
$isEditable = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["edit_mode"]) && $_POST["edit_mode"] === "true") {
        // User wants to enable editing
        $isEditable = true;
    } elseif (isset($_POST["first_name"])) {
        // User is submitting edited data
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $gender = $_POST["gender"];
        $dob = $_POST["dob"];
        $mobile = $_POST["mobile"];
        $grade = $_POST["grade"];
        $branch = $_POST["branch"];
        $institution = $_POST["institution"];
        $graduation_year = $_POST["graduation_year"];
        $courses = $_POST["courses"];

        $conn->query("UPDATE user_profiles SET 
            first_name='$first_name', last_name='$last_name', gender='$gender', dob='$dob', 
            mobile='$mobile', grade='$grade', branch='$branch', institution='$institution', 
            graduation_year='$graduation_year', courses='$courses'
            WHERE user_id='$userId'");

        $profileUpdated = true;
        $isEditable = false;

        // Reload updated data
        $userData = $conn->query("SELECT * FROM user_profiles WHERE user_id = '$userId'")->fetch_assoc();

        echo '<script>
            setTimeout(function() {
                window.location.href = "canti.php";
            }, 2000);
        </script>';
    }
} else {
    // Default: make form non-editable if profile is already filled
    $isEditable = empty($userData["first_name"]); // allow editing only if first fill
}
    // </script>';
// }

$requiredFields = ["first_name", "last_name", "gender", "dob", "mobile", "grade", "branch", "institution", "graduation_year", "courses"];
$filledFields = count(array_filter(array_intersect_key($userData, array_flip($requiredFields))));
$completionPercentage = round(($filledFields / count($requiredFields)) * 100);
$completionColor = ($completionPercentage >= 100) ? "green" : "red";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Smart Tutor</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background:#e3f2fd; }
        .header {
    background: navy;
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.header-side {
    width: 100px; /* or same as logout button width */
    display: flex;
    justify-content: flex-end;
}
.header-title {
    flex-grow: 1;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
}
.logout-btn {
    padding: 10px 20px;
    background: orangered;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
}


        .container { display: flex; height: calc(100vh - 80px); }
        .sidebar { width: 250px; background: #e3f2fd; padding: 20px; border-right: 1px solid #ccc; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar img { width: 100px; height: 100px; border-radius: 50%; cursor: pointer; display: block; margin: 0 auto; }
        .sidebar h3 { margin-top: 10px; text-align: center; }
        .sidebar button { width: 100%; margin: 10px 0; padding: 10px; border-radius: 5px; border: none; background-color:orangered; color: white; cursor: pointer; }
        .menu-section { display: absolute; margin-top: 20px; }
        .menu-section a { display: absolute; padding: 10px; background:red; margin: 5px 0; border-radius: 5px; text-decoration: none; color: #333; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
        .menu-section a:hover { background-color: #32cd32; color: white; }
        .cards-container {
            background: linear-gradient(to bottom right,rgb(162, 238, 191),rgb(117, 231, 222));
            border: solid #ccc 5px;
        padding: 30px;
        border-radius: 20px; /* Rounded corners */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Shadow effect */
        max-width: 1100px;
        margin: 30px auto;
    }

        .main { flex-grow: 1; padding: 30px; background: #eaeaea; overflow-y: auto; }
        .profile-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.2); display: none; width: 60%; margin-bottom: 20px; }
        input, select, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        button[type="submit"] { background-color: orangered; color: white; border: none; }

        .cards { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
        gap: 20px; 
        margin-top: 30px; 
        max-width: 1000px; /* Adjust width as needed */
        margin-left: auto;
        margin-right: auto;
        
    }
    /* Main Box with Green Border */
.main-container {
    border: 3px solid green; /* Green border around the main container */
    padding: 20px;
    margin: 20px;
    border-radius: 10px;
    background-color: #ffffff; /* Optional: You can set a background color for the main container */
}

/* Card Styling */
.card { 
    background: linear-gradient(to bottom right, #e3f2fd, #e3f2fd); 
    padding: 30px; 
    text-align: center; 
    border-radius: 15px; 
    box-shadow: 0 5px 15px rgba(44, 1, 1, 0.59); 
    transition: transform 0.3s ease; 
    width: 180px;  /* Fixed Width */
    height: 150px;
    border: 2px solid white; /* Blue border for each card */
}

/* Hover Effect for Cards */
.card:hover { 
    transform: translateY(-5px);  
}

/* Card Background Colors for Different Cards */
.card:nth-child(1) { background: linear-gradient(to bottom right, #ffebee, #ffcdd2); } /* Light Red */
.card:nth-child(2) { background: linear-gradient(to bottom right, #e3f2fd, #bbdefb); } /* Light Blue */
.card:nth-child(3) { background: linear-gradient(to bottom right, #e8f5e9, #c8e6c9); } /* Light Green */
.card:nth-child(4) { background: linear-gradient(to bottom right, #fff3e0, #ffe0b2); } /* Light Orange */
.card:nth-child(5) { background: linear-gradient(to bottom right, #ede7f6, #d1c4e9); }
.card:nth-child(9) { background: linear-gradient(to bottom right, #fce4ec, #f8bbd0); } /* Light Pink */
.card:nth-child(7) { background: linear-gradient(to bottom right, #e1f5fe, #b3e5fc); } /* Light Cyan */
.card:nth-child(8) { background: linear-gradient(to bottom right, #f3e5f5, #e1bee7); } /* Light Lavender */
.card:nth-child(6) { background: linear-gradient(to bottom right, #f9fbe7, #f0f4c3); } /* Light Yellow-Green */
.card:nth-child(10) { background: linear-gradient(to bottom right, #ffccbc, #ffab91); } /* Light Coral */

/* Table-like Layout (Grid) */
.main-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Automatic number of columns based on container width */
    grid-gap: 20px; /* Spacing between cards */
    padding: 20px;
}

/* Success Message Styling */
.success-message {
    color: green;
    font-weight: bold;
    text-align: center;
    display: <?= $profileUpdated ? 'block' : 'none' ?>;
    margin: 20px;
}

        #bx
        {
            background-color:#80daeb ;
            height: 380px;
            width: auto;
            border-radius: 5px;
            padding-top: 20px;
        }
        #bxs
        {
            background-color:#80daeb;
            height: 450px;
            width: auto;
            border-radius: 5px;
            padding-top: 27px;
        }
        #bxse
        {
            background-color:#80daeb;
            height: 450px;
            width: auto;
            border-radius: 5px;
            padding-top: 27px;
        }
        .review-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: auto;
        }
        .review-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 280px; /* Reduced width for better fit */
            text-align: center;
        }
        .review-profile-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }
        .review-name {
            font-weight: bold;
            margin: 10px 0;
        }
        .review-company {
            color: #555;
            font-size: 14px;
        }
        .review-text {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        @media (max-width: 1024px) {
            .review-container {
                justify-content: space-evenly;
            }
            .review-box {
                width: 45%; /* Two per row on medium screens */
            }
        }
        @media (max-width: 600px) {
            .review-box {
                width: 100%; /* One per row on small screens */
            }
        }
        
#student-talk {
    background-color: orangered;
    color: white;
    height: 40px; /* Increased height for better appearance */
    width: 140px; /* Increased width for better readability */
    text-align: center;
    line-height: 40px; /* Centers text vertically */
    font-weight: bold;
    border: white solid 3px;
    border-radius: 20px; /* Rounded edges */
    padding: 5px 10px; /* Adds spacing inside */
    margin: 10px auto; /* Centers the box horizontally */
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.2); /* Adds subtle shadow */
}
#Experts-Suggestione {
    background-color: orangered;
    color: white;
    height: 40px; /* Increased height for better appearance */
    width: 200px; /* Increased width for better readability */
    text-align: center;
   
    line-height: 40px; /* Centers text vertically */
    font-weight: bold;
    border: white solid 3px;
    border-radius: 20px; /* Rounded edges */
    padding: 5px 10px; /* Adds spacing inside */
    margin: 10px auto; /* Centers the box horizontally */
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.2); /* Adds subtle shadow */
}
#expert-talk {
    background-color: orangered;
    color: white;
    height: 40px; /* Increased height for better appearance */
    width: 140px; /* Increased width for better readability */
    text-align: center;
    border: white solid 3px;
    line-height: 40px; /* Centers text vertically */
    font-weight: bold;
    border-radius: 20px; /* Rounded edges */
    padding: 5px 10px; /* Adds spacing inside */
    margin: 10px auto; /* Centers the box horizontally */
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.2); /* Adds subtle shadow */
}
#careerheading {
    background-color: orangered;
    color: white;
    height: 40px; /* Increased height for better appearance */
    width: 240px; /* Increased width for better readability */
    text-align: center;
    line-height: 40px; /* Centers text vertically */
    font-weight: bold;
    border-radius: 20px; /* Rounded edges */
    padding: 5px 10px; /* Adds spacing inside */
    margin: 10px auto; /* Centers the box horizontally */
    display: flex;
    border: navy solid 4px;
    justify-content: center;
    align-items: center;
    box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.2); /* Adds subtle shadow */
}
.review-container-students {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: auto;
}

.review-box-students {
    width: 23%; /* Adjust width so 4 fit in one row */
    min-height: 240px;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #ddd;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    background-color: white;
}
        .review-profile-img-students {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ddd;
        }
        .review-name-students {
            font-weight: bold;
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }
        .review-text-students {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }
        @media (max-width: 1024px) {
    .review-box-students {
        width: 48%; /* 2 per row on tablets */
    }
}

@media (max-width: 600px) {
    .review-box-students {
        width: 100%; /* 1 per row on mobile */
    }
}
.review-container-studentse {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: auto;
}

.review-box-studentse {
    width: 23%; /* Adjust width so 4 fit in one row */
    min-height: 240px;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #ddd;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    background-color: white;
}
        .review-profile-img-studentse {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ddd;
        }
        .review-name-studentse {
            font-weight: bold;
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }
        .review-text-studentse {
        display: inline-block;
        padding: 8px 15px;
        margin: 5px;
        color: white;
        text-decoration: none;
        font-size: 16px;
        border-radius: 8px;
        width: 150px;
        height: 40px;
        text-align: center;
        line-height: 25px;
        box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease-in-out;
    }
    .review-text-studentse:hover {
        transform: scale(1.05);
    }
    .share-btn {
        background-color: #25D366;
        border:rgb(7, 102, 11)solid 3px;
    }
    .chat-btn {
        background-color: #007bff;
    }
}

@media (max-width: 600px) {
    .review-box-students {
        width: 100%; /* 1 per row on mobile */
    }
}
#k {
        width: auto;
        height: auto;
        padding: 10px;
    }

    #k a {
        display: block;
        /* background-color: #4CAF50; */
        background-color: white;
        color: orangered;
        text-decoration: none;
        padding: 15px;
        width: 180px;
        margin-left: 20px;
        margin: 10px 0; /* Adds space between sections */
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        padding-left: 10px;
        border: solid white 3px;
        transition: 0.3s;
    }

    #k a:hover {
        /* background-color: darkgreen; */
        background-color: #4CAF50;
        color: yellow;
    }
    .dark-mode {
    background-color: #121212;
    color: white;
}
.dark-theme {
    background-color: #121212;
    color: white;
}

.dark-mode .header {
    background-color: #1e1e1e;
}

.dark-mode .pdf-box {
    background-color: #222;
    color: white;
}
.career-section {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
       
        .career-image img {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
        }
        .career-content {
            margin-top: 20px;
        }
        .career-buttons {
            margin-top: 20px;
        }
        .invite-button {
            display: inline-flex;
            align-items: center;
            background:rgb(149, 0, 255);
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .invite-button img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        #x
        {
            /* background-color:#80daeb; */
            background-color: turquoise;
            border: white solid 5px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-side"></div> <!-- Left placeholder -->
    <div class="header-title">Smart Tutor</div> <!-- Centered title -->
    <form action="logout.php" method="POST" class="header-side">
        <button type="submit" class="logout-btn">Logout</button>
    </form>
</div>



<br><br>
<div class="container">
    <div class="sidebar">
        <img src="<?= !empty($userData['profile_image']) ? $userData['profile_image'] : 'uploads/profile.jpeg' ?>" id="toggleForm">
        <h3><?= $userData['first_name'] ?: $username ?></h3>
        <button onclick="toggleForm()" style="background:red; color: white; border: white  8px; padding: 10px 15px; border-radius: 15px;">
    Complete Profile ✏️
</button>
<div id="x">
        <div id="k">
    
    <a href="ts.html">🔊 Text-Speech</a>
    <a href="imagetotext.html">🔍 Image-Text</a>
    <a href="pdf.html">📄 PDF Maker</a>
    <a href="#bxse" >👩‍🏫 Experts</a>
    <a href="#careerheading">🎯 Career Path</a>

    <a href="l.html">🔤 Translator</a>
    </div>
    

</div>

        <div class="menu-section" id="menuSection" style="<?= ($completionPercentage < 100) ? 'display: none;' : 'display: block;' ?>">
            <!-- <button onclick="toggleMenu()">📂 Menu</button> -->
            <div id="menuLinks" style="display:none;">
                <a href="#">📈 Performance History</a>
                <a href="#">📚 My Courses</a>
                <a href="#">🛒 My Purchases</a>
                <a href="#">🧠 Expert Guidance</a>
                <a href="#">📝 Assignments</a>
                <a href="#">🎓 Lecture Notes</a>
                <a href="#">🧪 Practice Assessments</a>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="success-message" id="profileSavedMessage">✅ Profile Saved Successfully!</div>

        <div class="profile-box" id="profileForm">
            <form method="post">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?= $userData['first_name'] ?>">
                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?= $userData['last_name'] ?>">
                <label>Gender:</label>
                <select name="gender">
                    <option value="">Select</option>
                    <option value="Male" <?= ($userData['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($userData['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($userData['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
                <label>Date of Birth:</label>
                <input type="date" name="dob" value="<?= $userData['dob'] ?>">
                <label>Mobile:</label>
                <input type="text" name="mobile" value="<?= $userData['mobile'] ?>">
                <label>Grade:</label>
                <input type="text" name="grade" value="<?= $userData['grade'] ?>">
                <label>Branch:</label>
                <input type="text" name="branch" value="<?= $userData['branch'] ?>">
                <label>Institution:</label>
                <input type="text" name="institution" value="<?= $userData['institution'] ?>">
                <label>Graduation Year:</label>
                <input type="number" name="graduation_year" value="<?= $userData['graduation_year'] ?>">
                <label>Courses You Like:</label>
                <input type="text" name="courses" value="<?= $userData['courses'] ?>">
                <button type="submit" style="border: black; solid 3px">Save Profile</button>
            </form>
        </div>
        <div class="cards-container">
        <div class="cards">
            <div class="card">
                <h3><a href="chatbot.php" style="text-decoration: none; color: #333;">📘 Lecture Notes</a></h3>
            </div>
            <div class="card">
            <h3><a href="ass.php" style="text-decoration: none; color: #333;">📝 Assessment Questions </a></h3>
            </div>
            <div class="card">
                <h3><a href="quizz.php" style="text-decoration: none; color: #333;">🧠  Quiz Generator </a></h3>
            </div>
            <div class="card">
                <h3><a href="questions.php" style="text-decoration: none; color: #333;">🧩 Problem Solving</a></h3>
            </div>
            <div class="card">
                <h3><a href="ide.php" style="text-decoration: none; color: #333;">💻 Practice Coding</a></h3>
            </div>
            <div class="card">
                <h3><a href="bookmarks.html" style="text-decoration: none; color: #333;">📌 Student Bookmarks</a></h3>
            </div>
            <div class="card">
                <h3><a href="ex.php" style="text-decoration: none; color: #333;">🌐 External Resources</a></h3>
            </div>
            <div class="card">
                <h3><a href="mock.html" style="text-decoration: none; color: #333;">📜 Mock Tests</a></h3>
            </div>
            <div class="card">
                <h3><a href="el.html" style="text-decoration: none; color: #333;">🏛 Library System</a></h3>
            </div>
            <div class="card">
                <h3><a href="c.html" style="text-decoration: none; color: #333;">🎓 Online Courses</a></h3>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleForm() {
        const form = document.getElementById('profileForm');
        const menu = document.getElementById('menuSection');
        const menuLinks = document.getElementById('menuLinks');
        const completion = <?= $completionPercentage ?>;

        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            menuLinks.style.display = 'none';
        } else {
            form.style.display = 'none';
            if (completion >= 100) {
                menu.style.display = 'block';
            }
        }
    }
    document.getElementById("themeLink").addEventListener("click", function(event) {
    event.preventDefault(); // Prevent default anchor behavior
    document.body.classList.toggle("dark-mode");

    // Change link text based on current theme
    this.textContent = document.body.classList.contains("dark-mode") ? "Light Mode" : "Dark Mode";
});
document.querySelector("a").addEventListener("click", function (e) {
    e.preventDefault();  
    document.body.classList.toggle("dark-theme");
});

    function toggleMenu() {
        const links = document.getElementById('menuLinks');
        links.style.display = (links.style.display === 'none' || links.style.display === '') ? 'block' : 'none';
    }

    window.onload = function () {
        const menuSection = document.getElementById('menuSection');
        if (<?= $completionPercentage ?> >= 100) {
            menuSection.style.display = 'block';
        }
    };
    function shareOnWhatsApp(platform) {
        let randomTexts = [
            " Iam a Stundet From Smart Tutor, I am eagerly waiting for your guidance on " + platform + ".",
            " Iam a Stundet From Smart Tutor, Looking forward to an amazing session on " + platform + ".",
            " Iam a Stundet From Smart Tutor, Excited to connect with you on " + platform + " for guidance."
        ];
        let message = encodeURIComponent(randomTexts[Math.floor(Math.random() * randomTexts.length)]);
        let whatsappLink = "https://wa.me/?text=" + message;
        window.open(whatsappLink, "_blank");
    }
</script>

<div id="bx">
    <div id="expert-talk">
        Expert Talks
    </div><br>
<div class="review-container">
        <div class="review-box">
            <img src="assets/images/experts/hodmam.png" class="review-profile-img" alt="User">
            <div class="review-name">Dr. KARUNA</div>
            <div class="review-company">AIMLE HOD</div>
            <p class="review-text">Smart Tutor revolutionizes learning by providing personalized quizzes, enhancing student engagement, and improving knowledge retention.</p>
        </div>

        <div class="review-box">
            <img src="assets/images/experts/jesalmam.jpeg" class="review-profile-img" alt="User">
            <div class="review-name">Dr. JESAL VAROLIA</div>
            <div class="review-company"> Assistant Professor</div>
            <p class="review-text">With AI-powered question generation and a seamless UI, Smart Tutor makes exam preparation efficient and user-friendly.</p>
        </div>

        <div class="review-box">
            <img src="assets/images/experts/slmam.png"class="review-profile-img"  alt="User">
            <div class="review-name">Ms. P. NETHRASRI</div>
            <div class="review-company">Assistant Professor</div>
            <p class="review-text">Smart Tutor ensures accuracy in evaluation by offering real-time performance insights, correct answers, and personalized feedback</p>
        </div>

        <div class="review-box">
            <img src="assets/images/experts/aimam.png" class="review-profile-img" alt="User">
            <div class="review-name">Ms. V. MANASA</div>
            <div class="review-company">Assistant Professor</div>
            <p class="review-text">By simulating real exam conditions, Smart Tutor boosts confidence and readiness, making learning stress-free and effective.</p>
        </div>
    </div>
    </div>
    <div id="bxs">
    <div id="Student-talk">
       Student Talks
    </div><br>
    <div class="review-container-students">
        <div class="review-box-students">
            <img src="assets/images/students/mokshith.jpeg" class="review-profile-img-students" alt="Student">
            <div class="review-name-students">MOKSHITH REDDY</div>
            <p class="review-text-students">The lectures were very helpful, and the study material was well-organized.</p>
        </div>

        <div class="review-box-students">
            <img src="assets/images/students/koushik.jpeg" class="review-profile-img-students" alt="Student">
            <div class="review-name-students">KOUSHIK</div>
            <p class="review-text-students">I gained practical knowledge that helped me in my exams. Highly recommend!</p>
        </div>

        <div class="review-box-students">
            <img src="assets/images/students/saikiran.jpg" class="review-profile-img-students" alt="Student">
            <div class="review-name-students">SAI KIRAN</div>
            <p class="review-text-students">Smart Tutor’s lecture notes simplify complex topics, making studying more organized and efficient.</p>
        </div>
        <div class="review-box-students">
            <img src="assets/images/students/rohit.jpg" class="review-profile-img-students" alt="Student">
            <div class="review-name-students">ROHIT </div>
            <p class="review-text-students">I love how Smart Tutor generates topic-based quizzes instantly, it saves time and improves my accuracy!</p>
        </div>
        </div>
    </div>
    <div id="bxse">
    <div class="box-wrapper">
    <div id="Experts-Suggestione">Experts' Suggestion</div>
</div>
<br>
    <div class="review-container-studentse">
        <div class="review-box-studentse">
            <img src="assets/images/experts/hodmam.png" class="review-profile-img-studentse" alt="Student">
            <div class="review-name-studentse">Dr. KARUNA</div>
            <a href="https://wa.me/919849420327" class="review-text-studentse share-btn">
    <img src="assets/images/icons/whatsapp.jpeg" alt="WhatsApp" width="16" height="16"> Chat
</a>



<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        </div>

        <div class="review-box-studentse">
            <img src="assets/images/experts/jesalmam.jpeg" class="review-profile-img-studentse" alt="Student">
            <div class="review-name-studentse">Dr. JESAL VAROLIA</div>
            <a href="https://wa.me/919819774109" class="review-text-studentse share-btn">
    <img src="assets/images/icons/whatsapp.jpeg" alt="WhatsApp" width="16" height="16"> Chat
</a>



<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        </div>
        <div class="review-box-studentse">
            <img src="assets/images/experts/slmam.png" class="review-profile-img-studentse" alt="Student">
            <div class="review-name-studentse"> Ms. P. NETHRASRI</div>
            <a href="https://wa.me/919515728189" class="review-text-studentse share-btn">
    <img src="assets/images/icons/whatsapp.jpeg" alt="WhatsApp" width="16" height="16"> Chat
</a>



<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        </div>
        <div class="review-box-studentse">
            <img src="assets/images/experts/aimam.png" class="review-profile-img-studentse" alt="Student">
            <div class="review-name-studentse">Ms. V. MANASA </div>
            <a href="https://wa.me/919985869904" class="review-text-studentse share-btn">
    <img src="assets/images/icons/whatsapp.jpeg" alt="WhatsApp" width="16" height="16"> Chat
</a>



<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        </div>
        
    </div>
    <br><br><br><br><br>
    <div class="career-section">
        <div id="careerheading">Career Guidance</div>
        <br><br>
        <div class="career-image">
            <img src="assets/images/icons/career.jpeg" alt="Career Guidance">
        </div>
        <div class="career-content">
            <h2 class="career-heading">Smart Tutor offers career guidance to help students explore various career paths, develop essential skills, and prepare for future job opportunities.</h2>
            <br>
            <h3 style="color:navy">Send us your WhatsApp number and the topic you're interested in. Our experts will get in touch with you soon...</h3>
<form action="https://api.web3forms.com/submit" method="POST">
    <input type="hidden" name="access_key" value="3a934fa8-d9cc-4022-aaff-50bc3b38f927">

    <input type="text" name="whatsapp" placeholder="Enter your WhatsApp number" required>
    <input type="text" name="topic" placeholder="Topic you are interested in" required>
    
    <button type="submit">Submit</button>
</form>

            </div>
    </div>
<footer style="background-color:#838996; color: white; padding: 20px; text-align: center; color: white; padding: 40px 20px;">
    <div style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; justify-content: space-between;">
        
        <div style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <img src="assets/images/logo.jpeg" alt="Smart Tutor" style="width: 180px; border-radius:10px"><br><br>

            <p style="margin-top: 10px;">© Copyright 2025 <b>Smart Tutor</b> - All Rights Reserved</p><br>
            <hr><br>
            <p>AIML STUDENTS AB16 BATCH III YEAR (2022-2026)<br><br><hr><br>
            <p>GRIET Hyderabad | Telangana | 500090 <a href="https://maps.app.goo.gl/7najp1sneeKBKF4fA" target="_blank">
    <span role="img" aria-label="location">  Locate Us</span>
</a></p> <br>
<hr>

<br>
            <p>Email: <a href="mailto:smarttutorqueries@gmail.com" style="color: #f4b400;">Write Your Queries</a></p><br><hr>
        </div>

        <div style="flex: 1; margin-left: 100px;">
            <h3>Company</h3>
            <br><br>
            <ul style="list-style: none; padding: 0;">
                <li><a href="about.html" style="color: white; text-decoration: none;">About Us</a></li>
               <br><br>
                <li style="color: white; text-decoration: none;">Contact Us</li><br>
                <li><ul>+91 91009 37762</ul>
               <ul>+91 80746 82384</ul>
            <ul>+91 62811 54667</ul></li>
            </ul>
        </div>

        <div style="flex: 1; margin-left: 80px;">
            <h3>Support</h3>
            <br><br>
            <ul style="list-style: none; padding: 0;">
                <li><a href="privacy.html" style="color: white; text-decoration: none;">Privacy Policy</a></li>
            
            </ul>
        </div>

        <div style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <h3>Feedback</h3>
            <form action="https://api.web3forms.com/submit" method="POST">
    <input type="hidden" name="access_key" value="3a934fa8-d9cc-4022-aaff-50bc3b38f927">
    
    <input type="text" name="text" placeholder="Feedback Please..." required>
    <button type="submit">Submit</button>
</form>

            <h3 style="margin-top: 20px;">Follow Us</h3>
            <br>
            <div>
                <a href="https://www.instagram.com/smarttutor.in?igsh=M2I1bjgzam82MHJn" style="margin-right: 10px;"><img src="assets/images/icons/insta.jpeg" alt="Instagram" width="25"></a>
                <a href="https://x.com/smarttutorx" style="margin-right: 10px;"><img src="assets/images/icons/x.jpeg" alt="X" width="25"></a>
                <a href="https://www.linkedin.com/in/smart-tutor-smart-tutor-6bb609359/" style="margin-right: 10px;"><img src="assets/images/icons/linkedin.jpeg" alt="Linkedin" width="25"></a>
                <!-- <a href="https://www.facebook.com/profile.php?id=61574691188621"><img src="assets/images/icons/fb.jpeg" alt="YouTube" width="25"></a> -->
            </div>
        </div>
    </div>
</footer>

</body>
</html>