<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Griham</title>
    <link rel="stylesheet" href="./assets/css/nav.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f7fc;
            color: #333;
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 80px 5%;
        }

        .hero-content {
            max-width: 50%;
        }

        .hero h1 {
            font-size: 42px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin: 10px 0 20px;
        }

        .btn {
            background: #FF8000;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #cc6600;
        }

        .hero-image img {
            width: 100%;
            max-width: 450px;
            border-radius: 10px;
        }

        /* Mission Section */
        .mission {
            text-align: center;
            padding: 60px 5%;
            background: white;
        }

        .mission h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .mission-boxes {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .mission-box {
            background: #FF8000;
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 30%;
            transition: 0.3s;
        }

        .mission-box:hover {
            background: #cc6600;
        }

        /* Objectives Section */
        .objectives {
            text-align: center;
            padding: 50px 5%;
            background: #f4f7fc;
        }

        .objectives ul {
            list-style: none;
            padding: 0;
        }

        .objectives li {
            font-size: 18px;
            margin-bottom: 10px;
        }

        /* Methodology */
        .methodology {
            text-align: center;
            padding: 60px 5%;
            background: white;
        }

        .steps {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .step {
            background: #0056D2;
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 45%;
            transition: 0.3s;
        }

        .step:hover {
            background: #003d99;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-content {
                max-width: 100%;
            }

            .hero-image img {
                margin-top: 20px;
            }

            .mission-boxes {
                flex-direction: column;
            }

            .mission-box {
                width: 100%;
            }

            .steps {
                flex-direction: column;
            }

            .step {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<?php require("navbar.php"); ?>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Griham</h1>
            <p>Crafting Accommodation into Homes - Your One-Stop Solution for Seamless Relocation.</p>
        </div>
        <div class="hero-image">
            <img src="./assets/images/acc.jpg" alt="Accommodation">
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission">
        <h2>Our Mission</h2>
        <p>Griham is designed to simplify accommodation searches, connect users with essential services, and create a stress-free relocation experience.</p>
        <div class="mission-boxes">
            <div class="mission-box">
                <h3>Easy Rentals</h3>
                <p>Find verified PGs, rooms, and rentals with complete details.</p>
            </div>
            <div class="mission-box">
                <h3>Essential Services</h3>
                <p>Seamless access to food, laundry, and emergency services.</p>
            </div>
            <div class="mission-box">
                <h3>Roommate Matching</h3>
                <p>Find compatible roommates based on preferences.</p>
            </div>
        </div>
    </section>

    <!-- Objectives Section -->
    <section class="objectives">
        <h2>Our Objectives</h2>
        <ul>
            <li>✔ Provide a centralized system for accommodation and essential services.</li>
            <li>✔ Offer a user-friendly web-based solution with reliable rental options.</li>
            <li>✔ Enable search filters to match users with suitable accommodations.</li>
            <li>✔ Ensure secure and verified listings for trust and transparency.</li>
        </ul>
    </section>

    <!-- Methodology Section -->
    <section class="methodology">
        <h2>How It Works</h2>
        <div class="steps">
            <div class="step">
                <h3>1. Search & Browse</h3>
                <p>Users can explore available listings with verified amenities and prices.</p>
            </div>
            <div class="step">
                <h3>2. List Your Property</h3>
                <p>Property owners can register and list their accommodations after admin verification.</p>
            </div>
            <div class="step">
                <h3>3. Access Essential Services</h3>
                <p>Users can avail food, laundry, and health services directly from the platform.</p>
            </div>
            <div class="step">
                <h3>4. Find a Roommate</h3>
                <p>Users can match with potential roommates based on shared preferences.</p>
            </div>
        </div>
    </section>

    <?php require("./footer.php"); ?>
</body>

</html>