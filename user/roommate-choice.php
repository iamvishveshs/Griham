<?php
require("./check.php");
// Check if user has roommate_accommodation entry
$user_id = intval($_SESSION['user_id']);
$sql = "SELECT accommodation_id FROM roommate_accommodations WHERE user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    // User has roommate_accommodation entry
    $hasAccommodation = true;
} else {
    // User does not have roommate_accommodation entry
    $hasAccommodation = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roommate Choice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .choice-container {
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }
        a{
            text-decoration: none;
        }
        .choice-choice-cards {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        .choice-card:hover {
            transform: scale(1.05);
        }
        .choice-card:nth-child(1) {
            background-color: #ffe4ef;
        }
        .choice-card:nth-child(2) {
            background-color: #fff4cc;
        }
        .choice-card h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .choice-card img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }
        .choice-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width:250px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            max-width: 300px;
            margin: 20px auto;
        }
        .choice-card img {
            width: 100px;
            margin-bottom: 10px;
        }
        .choice-card h2 {
            margin-bottom: 5px;
        }
        @media (max-width: 600px) {
            .choice-choice-cards {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="choice-container">
        <h1>Post Your Requirement</h1>
        <p>Find your perfect roommate or room effortlessly. Just post your requirement and let the matching begin!</p>
        <div class="choice-choice-cards">
            <a class="choice-card" href="./location.php?refferer=roommate">
                <img src="../assets/images/home.png" alt="Room/Flat">
                <h2>Need Room</h2>
                <p>with roommate</p>
            </a>
            <?php if ($hasAccommodation) : ?>
            <a class="choice-card" href="./show_room_detail.php">
                <img src="../assets/images/home_roommates.png" alt="Roommate">
                <h2>Your Room Details </h2>
                <p>for your roommate</p>
                <p>View details</p>
            </a>
        <?php else : ?>
            <a class="choice-card" href="./list-room.php">
                <img src="../assets/images/home_roommates.png" alt="Roommate">
                <h2>Need Roommate</h2>
                <p>for your room</p>
                <p>List Your Room</p>
            </a>
        <?php endif; ?>
        </div>
    </div>
</body>
</html>