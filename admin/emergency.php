<?php
require("./check.php");
$id = $_SESSION["user_id"];
$hospital_count=mysqli_num_rows(mysqli_query($conn,"SELECT `service_id` FROM `emergency_services` WHERE category_id='1'"));
$fbrigade_count=mysqli_num_rows(mysqli_query($conn,"SELECT `service_id` FROM `emergency_services` WHERE category_id='2'"));
$pstation_count=mysqli_num_rows(mysqli_query($conn,"SELECT `service_id` FROM `emergency_services` WHERE category_id='3'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard </title>
    <?php
    require("./style-files.php");
    ?>
    <style>
        .container {
            margin: 20px auto;
            padding: 20px 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        h1 {
            font-weight: 900;
            font-size: 32px;
            margin-bottom: 50px;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 70px;
            justify-items: center;
        }
        .cards-4 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
            place-items: center;
        }
        .cards-4 >* {
          width:100%;
        }
        .card {
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e2246;
            margin-bottom: 15px;
            gap: 10px;
        }
        .icon i {
            height: 64px;
            width: 64px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }
        .fa-stethoscope {
            background: linear-gradient(to bottom right, #bbcbfb, #f1d4ff);
        }
        .fa-house-fire {
            background: linear-gradient(to bottom right, #fbbbda, #ffd4d4);
        }
        .fa-handcuffs {
            background: linear-gradient(to bottom right, #bbebfb, #efd4ff);
        }
        .fa-shirt {
            background: linear-gradient(to bottom right, #bbfbe4, #d4e0ff);
        }
        .fa-dumbbell {
            background: linear-gradient(to bottom right, #bbd8fb, #ffd4d4);
        }
        .fa-plus {
            background: linear-gradient(to bottom right, #ffdec7, #d4d9ff);
        }
        .title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        /* Default state for .count (not selected) */
        .count,
        .count:visited {
            display: block;
            text-decoration: none;
            color: #1e2246;
            border: 1px solid #c2c5e2;
            border-radius: 8px;
            padding: 10px 30px;
            margin-top: 40px;
            transition: background-color 0.3s ease;
        }
        /* Hover state */
        .count:hover {
            background-color: #f1f5fe;
        }
        /* Selected state */
        .count.selected {
            background-color: #1e2246;
            color: #fff;
            border: 2px solid #c2c5e2;
            transform: scale(1.05);
            transition: all 0.3s ease;
        }
        /* Responsive Design for Medium Screens (980px) */
        @media (max-width: 980px) {
            .cards{
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .cards-4 {
                max-width: 100%;
            overflow-x: scroll;
        }
            .icon {
                font-size: 2rem;
                gap: 8px;
            }
            .title {
                font-size: 1.1rem;
            }
            .count {
                font-size: 0.9rem;
            }
        }
        /* Responsive Design for Smaller Screens (768px and below) */
        @media (max-width: 768px) {
            .cards {
                grid-template-columns: 1fr;
            }
            .cards-4 {
                max-width: 100%;
            overflow-x: scroll;
        }
        }
        /* Extra Small Screen Adjustments */
        @media (max-width: 480px) {
            .cards-4 {
                max-width: 100%;
            overflow-x: scroll;
        }
        .container {
            margin: 20px 5px;
            padding: 5px;
        }
        .c-dashboardInfo
        {
            margin:5px;
        }
            h1 {
                font-size: 28px;
            }
            .title {
                font-size: 1rem;
            }
            .count {
                padding: 8px 20px;
                font-size: 0.8rem;
            }
        }
    </style>
    <style>
        .c-dashboardInfo {
            margin-bottom: 15px;
        }
        .c-dashboardInfo .wrap {
            background: #ffffff;
            box-shadow: 2px 10px 20px rgba(0, 0, 0, 0.1);
            border-radius: 7px;
            text-align: center;
            position: relative;
            overflow: hidden;
            padding: 40px 25px 20px;
            height: 100%;
        }
        .c-dashboardInfo__title,
        .c-dashboardInfo__subInfo {
            color: #6c6c6c;
            font-size: 1.18em;
        }
        .c-dashboardInfo span {
            display: block;
        }
        .c-dashboardInfo__count {
            font-weight: 600;
            font-size: 2.5em;
            line-height: 64px;
            color: #323c43;
        }
        .c-dashboardInfo .wrap:after {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            content: "";
        }
        .c-dashboardInfo:nth-child(1) .wrap:after {
            background: linear-gradient(82.59deg, #00c48c 0%, #00a173 100%);
        }
        .c-dashboardInfo:nth-child(2) .wrap:after {
            background: linear-gradient(81.67deg, #0084f4 0%, #1a4da2 100%);
        }
        .c-dashboardInfo:nth-child(3) .wrap:after {
            background: linear-gradient(69.83deg, #0084f4 0%, #00c48c 100%);
        }
        .c-dashboardInfo:nth-child(4) .wrap:after {
            background: linear-gradient(81.67deg, #ff647c 0%, #1f5dc5 100%);
        }
        .c-dashboardInfo__title svg {
            color: #d7d7d7;
            margin-left: 5px;
        }
        .MuiSvgIcon-root-19 {
            fill: currentColor;
            width: 1em;
            height: 1em;
            display: inline-block;
            font-size: 24px;
            transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
            user-select: none;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <?php require("./navbar.php"); ?>
    <div class="container">
<h1>Emergency Services</h1>
        <?php require("./show-message.php"); ?>
        <div id="root" class="cards-4">
            <div class="c-dashboardInfo ">
                <div class="wrap">
                    <h4 class=" c-dashboardInfo__title">Hospital </h4><span class=" c-dashboardInfo__count"><?php echo $hospital_count;?></span>
                </div>
            </div>
            <div class="c-dashboardInfo">
                <div class="wrap">
                    <h4 class=" c-dashboardInfo__title">Fire Station</h4><span class=" c-dashboardInfo__count"><?php echo $fbrigade_count;?></span>
                </div>
            </div>
            <div class="c-dashboardInfo">
                <div class="wrap">
                    <h4 class=" c-dashboardInfo__title">Police Station</h4><span class=" c-dashboardInfo__count"><?php echo $pstation_count;?></span>
                </div>
            </div>
        </div>
        <div class="cards">
            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-stethoscope"></i>
                    <p class="title">Hospital</p>
                </div>
                <a href="hospital.php" class="count">View</a>
            </div>
            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-house-fire"></i>
                    <p class="title">Fire Station</p>
                </div>
                <a href="fire_station.php" class="count" id="default-selected">View</a>
            </div>
            <div class="card">
                <div class="icon">
                    <i class="fa-solid fa-handcuffs"></i>
                    <p class="title">Police Station</p>
                </div>
                <a href="./police_station.php" class="count">View</a>
            </div>
        </div>
    </div>
    <?php
    require("footer.php");
    require("./script-files.php");
    ?>
</body>
</html>