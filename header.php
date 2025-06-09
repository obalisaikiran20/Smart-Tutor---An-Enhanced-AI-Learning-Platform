<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Header Styles */
        .header {
            background-color: navy;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            height: 80px;
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
        /* Body padding to avoid content hiding behind the header */
        body { padding-top: 50px; }

        /* Logout Button Styles */
        #buttons {
            position: absolute;
            top: 10px;
            right: 20px;
        }

        #buttons button {
            background-color: orange;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Logout Button positioned at top-right -->
    <!-- <div id="buttons">
        <button>Logout</button>
    </div> -->

    <!-- Header Section -->
    <div class="header"><button class="back-button" onclick="window.location.href='canti.php';">Back</button>
        Smart Tutor
    </div>
</body>
</html>
