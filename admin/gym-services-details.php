<?php
require("./check.php");

if (isset($_GET['token'])) {
    $service_id = base64_decode($_GET['token'], true);

    if ($service_id === false || !ctype_digit($service_id)) {
        die("Invalid request.");
    }

    $result = mysqli_query($conn, "SELECT gm.service_id, 
                 gm.name, 
                 gm.contact_number, 
                 gm.description, 
                 gm.main_image,
                 gm.opening_hours,
                 gm.closing_hours,
                 adr.city, 
                 CONCAT(adr.village, ', ', adr.po, ', ', adr.tehsil, ', ', adr.district, ', ', adr.state) AS address, 
                 adr.pincode, 
                 GROUP_CONCAT(DISTINCT CONCAT(am.amenity_name, ':', am.icon_class) ORDER BY am.amenity_name SEPARATOR ', ') AS amenities,
                 GROUP_CONCAT(DISTINCT img.image_url ORDER BY img.image_id SEPARATOR ', ') AS images
          FROM gym_services gm
          LEFT JOIN addresses adr ON gm.address_id = adr.address_id
          LEFT JOIN gym_services_amenities gma ON gm.service_id = gma.gym_service_id
          LEFT JOIN amenities am ON gma.amenity_id = am.amenity_id
          LEFT JOIN images img ON gm.service_id = img.entity_id AND img.entity_type = 'gym_service'
          WHERE gm.service_id = '$service_id'
          GROUP BY gm.service_id");

    if ($property = mysqli_fetch_assoc($result)) {
    } else {
        die("Property not found.");
    }
    mysqli_close($conn);
} else {
    require("./page-parameter-access.php");
    exit();
}
?>


<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($property['name']); ?> - Gym Services Details</title>
    <?php require("./style-files.php"); ?>
    <style>
        /* Hero section with slider */
        .hero-section {
            display: flex;
            padding: 3rem 2rem;
            align-items: center;
            background-color: #fff;
            margin: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .hero-image {
            flex: 1;
            padding-right: 2rem;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            height: 400px;
        }

        .slider {
            display: flex;
            transition: transform 0.5s ease;
            height: 400px;
        }

        .slide {
            min-width: 100%;
            height: 100%;
            position: relative;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slider-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .slider-dot.active {
            background-color: #ff5e00;
        }

        .image-view-more-section {
            margin-top: 1rem;
            text-align: center;
        }

        .image-view-more-btn {
            display: inline-block;
            color: #ff5e00;
            background-color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            border: 2px solid #ff5e00;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .image-view-more-btn:hover {
            background-color: #ff5e00;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(255, 94, 0, 0.3);
        }

        .intro-section {
            flex: 1;
            padding-left: 2rem;
        }

        .intro-section h1 {
            font-size: 2.5rem;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }

        .intro-section h1 span {
            color: #ff5e00;
        }

        .intro-section p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #555;
            margin-bottom: 1.5rem;
        }

        .cta-button {
            display: inline-block;
            background-color: #ff5e00;
            color: white;
            padding: 0.8rem 1.8rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .cta-button:hover {
            background-color: #e05500;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(255, 94, 0, 0.3);
        }

        /* Content sections */
        .content-section {
            background-color: #fff;
            margin: 2rem;
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            border-bottom: 2px solid #ff5e00;
            padding-bottom: 1rem;
        }

        .section-header h2 {
            font-size: 1.8rem;
            color: #1a1a1a;
            margin-right: 1rem;
        }

        .section-header .icon {
            color: #ff5e00;
            font-size: 1.8rem;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: space-between;
        }

        .feature-card {
            flex: 1;
            min-width: 250px;
            background-color: #f9f9f9;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card h3 {
            color: var(--theme-color);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .feature-card p {
            color: #555;
            line-height: 1.6;
        }

        .testimonials {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }



        .price {
            color: #ff5e00;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 1rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {


            .hero-section {
                flex-direction: column;
                margin: 1rem;
                padding: 1.5rem;
            }

            .hero-image {
                padding-right: 0;
                margin-bottom: 1.5rem;
                width: 100%;
                height: 300px;
            }

            .intro-section {
                padding-left: 0;
                text-align: center;
            }

            .content-section {
                margin: 1rem;
                padding: 1.5rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .section-header h2 {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <?php require("./navbar.php"); ?>

    <section class="hero-section">
        <div class="hero-image">
            <div class="slider">
                <div class="slide">
                    <img src="../assets/uploads/gym-services/<?php echo htmlspecialchars($property['main_image']); ?>" alt="Gym Interior">
                </div>
                <?php
                $images = array_filter(array_map('trim', explode(",", $property["images"])));
                foreach ($images as $image) {
                ?>
                    <div class="slide">
                        <img src="../assets/uploads/gym-services/<?php echo htmlspecialchars($image); ?>" alt="Gym Interior">
                    </div>
                <?php
                }
                ?>


            </div>
            <div class="slider-nav">
                <div class="slider-dot active"></div>

                <?php
                $images = array_filter(array_map('trim', explode(",", $property["images"])));
                foreach ($images as $image) {
                ?>
                    <div class="slider-dot"></div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="intro-section">
            <h1><?php echo htmlspecialchars($property['name']); ?> </h1>
            <p><strong><i class="fas fa-dumbbell"></i></strong> Gym & Fitness</p>
            <p><strong><i class="fas fa-map-marker-alt"></i></strong> <?php echo htmlspecialchars($property['city']); ?></p>

            <p><strong><i class="fas fa-clock"></i></strong> <?php echo date('g:i A', strtotime(htmlspecialchars($property['opening_hours']))) . " - " . date('g:i A', strtotime(htmlspecialchars($property['closing_hours']))); ?></p>

            
        </div>
    </section>

    <!-- Features Section -->
    <section class="content-section">
        <div class="section-header">
            <div>
                <i class="fas fa-dumbbell icon"></i>
                <h2>OUR FACILITIES</h2>
            </div>
        </div>
        <div class="features">
            <?php
            foreach (explode(",", $property["amenities"]) as $amenity) {
                $amenityArray = explode(":", $amenity) ?>
                <div class="feature-card">
                    <h3><?php echo "<i class='" . htmlspecialchars($amenityArray[1]) . "'></i> &nbsp;" . htmlspecialchars($amenityArray[0]); ?></h3>
                </div>
            <?php } ?>

        </div>
    </section>
    <!-- Features Section -->
    <section class="content-section">
        <div class="section-header">
            <div>
                <i class="fas fa-dumbbell icon"></i>
                <h2>ABOUT US</h2>
            </div>

        </div>
        <div>
            <p><?php echo $property['description']; ?></p>

        </div>
    </section>



    <!-- Contact Section -->
    <section class="content-section">
        <div class="section-header">
            <div>
                <i class="fas fa-map-marker-alt icon"></i>
                <h2>FIND US</h2>
            </div>
        </div>
        <div class="features">
            <div class="feature-card">
                <h3>Location</h3>
                <p><?php echo htmlspecialchars($property['address']); ?></p>
            </div>
            <div class="feature-card">
                <h3>Hours</h3>
                <p><?php echo date('g:i A', strtotime(htmlspecialchars($property['opening_hours']))) . " - " . date('g:i A', strtotime(htmlspecialchars($property['closing_hours']))); ?></p>
            </div>
            <div class="feature-card">
                <h3>Contact</h3>
                <p>Phone: <?php echo $property['contact_number']; ?></p>
            </div>
        </div>
    </section>

    <script>
        // Image slider functionality
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        const slideCount = slides.length;

        function goToSlide(n) {
            currentSlide = (n + slideCount) % slideCount;
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;

            // Update dots
            dots.forEach(dot => dot.classList.remove('active'));
            dots[currentSlide].classList.add('active');
        }

        // Set up dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
            });
        });

        // Auto-advance slides every 5 seconds
        setInterval(() => {
            goToSlide(currentSlide + 1);
        }, 3000);
    </script>
    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>
</body>

</html>