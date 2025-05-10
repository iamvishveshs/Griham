<?php

require("database.php");
$query = "SELECT * FROM meal_services ms left join addresses ad ON  ms.address_id=ad.address_id";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <title>Rooms </title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <?php
    require("./style-files.php");
    ?>
    <style>
        .popup {
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            text-align: center;
        }

        .popup .popup__content {
            width: fit-content;
            overflow: auto;
            padding: 20px 50px;
            background: white;
            color: black;
            position: relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-sizing: border-box;
        }

        .popup .popup__content p
        {
            font-weight: bold;
            font-size: 1rem;
        }
        .popup .popup__content .close {
            position: absolute;
            right: 20px;
            top: 20px;
            width: 20px;
            display: block;
        }

        .popup .popup__content .close span {
            cursor: pointer;
            position: fixed;
            width: 20px;
            height: 3px;
            background: #099ccc;
        }

        .popup .popup__content .close span:nth-child(1) {
            transform: rotate(45deg);
        }

        .popup .popup__content .close span:nth-child(2) {
            transform: rotate(135deg);
        }
    </style>
</head>

<body>
    <section class="popup">
        <div class="popup__content">
            <div class="close">
                <span></span>
                <span></span>
            </div>
           <p>In order to proceed, you have to login</p>
           <a href="./login.php" class="btn">Login</a>
        </div>
    </section>
    <?php require("./navbar.php"); ?>
    <div class="page-container">
        <h2>Meal Listings</h2>

        <p class="success"><?php
                            if (isset($_SESSION['success'])) {
                                echo $_SESSION['success']; // Show success
                                unset($_SESSION['success']); // Remove success after displaying
                            }            ?></p>
        <p class="error"><?php
                            if (isset($_SESSION['error'])) {
                                echo $_SESSION['error']; // Show error
                                unset($_SESSION['error']); // Remove error after displaying
                            }            ?></p>
        <div class="listing-container">

            <?php while ($listing = mysqli_fetch_assoc($result)) { ?>
                <div class="property-card">
                    <img src="<?php echo "./assets/uploads/meal-services/" . $listing['main_image']; ?>"
                        class="property-image" alt="Meal Service Image">
                    <h3 class="address"><?php echo $listing['name']; ?></h3>
                    <span class="tag"><?php echo $listing['city']; ?></span>
                    <button class="popup-button custom-button">View Details</button>
                    <br>


                </div>
            <?php } ?>

        </div>
    </div>
    <script>
        $(".popup-button").click(function() {
            $(".popup").fadeIn(500);
        });
        $(".close").click(function() {
            $(".popup").fadeOut(500);
        });
    </script>
    <?php require("./footer.php"); ?>
    <?php
    require("./script-files.php");
    ?>
</body>

</html>