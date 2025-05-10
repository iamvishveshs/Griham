<?php
require("./check.php");

$id = $_SESSION["user_id"];
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
     <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</head>

<body>
    <?php require("./navbar.php"); ?>




    <!-- Hero Section -->

    <section class="profile-section">

        <div>
            <h1>Welcome!</h1>
            <h2 id="username">{ <?php echo $_SESSION['name']; ?> }</h2>
        </div>
    </section>

    <div class="saved-container margin-top">

        <div>
            <h3 class="center margin-top ">Services</h3>
        </div>
        <div class="categories">
            <div class="category" style="background-color: #F7ECFF;" data-color="#D0A6FF"
                onclick="redirectTo('browse-listings.php')">
                <i class="fa-solid fa-house"></i> Your Owned PG/Room/Apartment
            </div>

            <div class="category" style="background-color: #D7F9F3;" data-color="#62E4C1"
                onclick="redirectTo('meal-services.php')">
                <i class="fa-solid fa-utensils"></i> Your Owned Dhaba/Tiffin/Cafe
            </div>

            <div class="category" style="background-color: #E2FFD4;" data-color="#8DD76C"
                onclick="redirectTo('laundry.php')">
                <i class="fa-solid fa-shirt"></i> Your Owned Laundries
            </div>

            <div class="category" style="background-color: #DBD5EC;" data-color="#8A7DBC"
                onclick="redirectTo('gym.php')">
                <i class="fa-solid fa-dumbbell"></i> Your Owned Gym 
            </div>
        </div>


    </div>



    <!-- Your Properties Section -->
    <?php

    $listing_query = "SELECT * FROM accommodation_services WHERE user_id='$id' ORDER BY service_id DESC LIMIT 3";
    $listing_result = mysqli_query($conn, $listing_query);
    if (mysqli_num_rows($listing_result) > 0) {
    ?>
        <section class="saved-container">
            <div class="saved-container-header">
                <h2>Your Owned PG/Room/Apartment</h2>
                <a class="view-all-btn" href="./browse-listings.php">View All</a>
            </div>

            <div class="saved-card-list">
                <?php
                while ($listing = mysqli_fetch_assoc($listing_result)) { ?>
                    <div class="saved-card">
                        <img src="<?php echo "../assets/uploads/accommodation-images/" . $listing['main_image']; ?>" alt="">
                        <div class="saved-card-info">
                            <h4>
                                <?php $token = base64_encode($listing['service_id']);
                                echo '<a href="property-details.php?token=' . urlencode($token) . '">' . $listing['property_name'] . '</a>'; ?></h4>
                        </div>

                    </div>

                <?php } ?>


            </div>
        </section>
    <?php } ?>

    <!-- Your Meal Section -->
    <?php

    $meal_query = "SELECT * FROM meal_services WHERE user_id='$id' ORDER BY service_id DESC LIMIT 3";
    $meal_result = mysqli_query($conn, $meal_query);
    if (mysqli_num_rows($meal_result) > 0) {
    ?>
        <section class="saved-container">
            <div class="saved-container-header">
                <h2>Your Owned Dhaba/Tiffin/Cafe</h2>
                <a class="view-all-btn" href="./meal-services.php">View All</a>
            </div>

            <div class="saved-card-list">
                <?php
                while ($meal = mysqli_fetch_assoc($meal_result)) { ?>
                    <div class="saved-card">
                        <img src="<?php echo "../assets/uploads/meal-services/" . $meal['main_image']; ?>" alt="">
                        <div class="saved-card-info">
                            <h4>
                                <?php $token = base64_encode($meal['service_id']);
                                echo '<a href="meal-services-details.php?token=' . urlencode($token) . '">' . $meal['name'] . '</a>'; ?></h4>
                        </div>

                    </div>

                <?php } ?>


            </div>
        </section>
    <?php } ?>

    <!-- Your Meal Section -->
    <?php

    $gym_query = "SELECT * FROM gym_services WHERE user_id='$id' ORDER BY service_id DESC LIMIT 3";
    $gym_result = mysqli_query($conn, $gym_query);
    if (mysqli_num_rows($gym_result) > 0) {
    ?>
        <section class="saved-container">
            <div class="saved-container-header">
                <h2>Your Owned Gym</h2>
                <a class="view-all-btn" href="./gym.php">View All</a>
            </div>

            <div class="saved-card-list">
                <?php
                while ($gym = mysqli_fetch_assoc($gym_result)) { ?>
                    <div class="saved-card">
                        <img src="<?php echo "../assets/uploads/gym-services/" . $gym['main_image']; ?>" alt="">
                        <div class="saved-card-info">
                            <h4>
                                <?php $token = base64_encode($gym['service_id']);
                                echo '<a href="gym-services-details.php?token=' . urlencode($token) . '">' . $gym['name'] . '</a>'; ?></h4>
                        </div>

                    </div>

                <?php } ?>


            </div>
        </section>
    <?php } ?>

    <?php require("./footer.php"); ?>

   
    <?php
    require("./script-files.php");
    ?>
</body>

</html>