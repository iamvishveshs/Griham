<?php
require("./check.php");
if (isset($_GET['token'])) {
    $service_id = base64_decode($_GET['token'], true);
    if ($service_id === false || !ctype_digit($service_id)) {
        die("Invalid request.");
    }
    $result = mysqli_query($conn, "SELECT ms.service_id,
                 ms.name,
                 ms.category,
                 ms.contact_number,
                 ms.description,
                 ms.main_image,
                 adr.city,
                 CONCAT(adr.village, ', ', adr.po, ', ', adr.tehsil, ', ', adr.district, ', ', adr.state) AS address,
                 adr.pincode,
                 GROUP_CONCAT(DISTINCT CONCAT(am.amenity_name, ':', am.icon_class) ORDER BY am.amenity_name SEPARATOR ', ') AS amenities,
                 GROUP_CONCAT(DISTINCT img.image_url ORDER BY img.image_id SEPARATOR ', ') AS images
          FROM meal_services ms
          LEFT JOIN addresses adr ON ms.address_id = adr.address_id
          LEFT JOIN meal_services_amenities msa ON ms.service_id = msa.meal_service_id
          LEFT JOIN amenities am ON msa.amenity_id = am.amenity_id
          LEFT JOIN images img ON ms.service_id = img.entity_id AND img.entity_type = 'meal_service'
          WHERE ms.service_id = '$service_id'
          GROUP BY ms.service_id");
    if ($property = mysqli_fetch_assoc($result)) {
    } else {
        die("Property not found.");
    }
    mysqli_close($conn);
} else {
    die("Access Denied");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($property['name']); ?> - Meal Services Details</title>
    <?php require("./style-files.php"); ?>
    <style>
        /* MAIN CONTAINER */
        .container {
            max-width: 1100px;
            margin: 40px auto 80px;
            padding: 0 15px;
        }
        /* RE-DESIGNED, INTERACTIVE PROPERTY CARD */
        .property-details-card {
            background: #fff;
            display: flex;
            border-radius: var(--theme-border-radius);
            overflow: hidden;
            box-shadow: var(--theme-shadow);
            margin-bottom: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .property-details-card .image-section {
            flex: 0 0 35%;
            overflow: hidden;
        }
        .property-details-card .image-section img {
            width: 100%;
            height: 300px;
            object-fit: fill;
            transition: transform 0.3s ease;
        }
        .property-details-card:hover .image-section img {
            transform: scale(1.05);
        }
        .property-details-card .info-section {
            flex: 0 0 65%;
            padding: 30px;
            background: linear-gradient(135deg, #ffffff, var(--theme-secondary-bg));
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .property-details-card .info-section div h2 {
            margin: 0 0 15px;
            font-size: 26px;
            font-weight: 600;
            color: var(--theme-color);
        }
        .header-text {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            border-bottom: 2px solid var(--theme-color-dark);
            padding-bottom: 5px;
        }
        .header-buttons a {
            display: block;
            padding: 10px 10px;
            border-radius: var(--theme-border-radius);
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            height: auto;
            width: auto;
            transition: var(--theme-transition);
        }

        .property-details-card .info-section p {
            font-size: 16px;
            margin-bottom: 15px;
            line-height: 1.6;
            color: var(--theme-secondary-color);
        }
        .property-details-card .info-section a {
            color: var(--theme-color);
            text-decoration: none;
            font-weight: 600;
            transition: border-bottom 0.3s ease;
        }
        .property-details-card .info-section a:hover {
            border-bottom: 1px solid var(--theme-color);
        }
        /* CAROUSEL */
        .carousel {
            position: relative;
            background: #fff;
            overflow: hidden;
            border-radius: var(--theme-border-radius);
            box-shadow: var(--theme-shadow);
            margin-bottom: 40px;
        }
        .carousel-images {
            display: flex;
            transition: var(--theme-transition);
        }
        .carousel-images img {
            flex: 0 0 100%;
            width: 100%;
            height: 400px;
            object-fit: contain;
            display: block;
            margin: auto;
        }
        .carousel-buttons {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            transform: translateY(-50%);
        }
        .carousel-buttons button {
            background: rgba(0, 0, 0, 0.6);
            border: none;
            border-radius: 50%;
            padding: 20px 25px;
            cursor: pointer;
            transition: var(--theme-transition);
            color: #fff;
            font-size: 18px;
        }
        .carousel-buttons button:hover {
            background: var(--theme-hover-color);
        }
        .carousel-indicators {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }
        .carousel-indicators span {
            width: 10px;
            height: 10px;
            background: rgba(103, 97, 97, 0.3);
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
        }
        .carousel-indicators span.active {
            background: var(--theme-dark-color);
        }
        /* TABS */
        .tabs {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            background: #fff;
            box-shadow: var(--theme-shadow);
            border-radius: var(--theme-border-radius);
            padding: 10px;
            border: var(--theme-border);
            margin-bottom: 40px;
        }
        .tabs .tab {
            font-size: 16px;
            font-weight: 600;
            color: var(--theme-secondary-color);
            cursor: pointer;
            transition: var(--theme-transition);
            padding-bottom: 6px;
        }
        .tabs .tab.active {
            color: var(--theme-color);
            border-bottom: 2px solid var(--theme-color);
        }
        .tab-content {
            background: #fff;
            padding: 25px;
            box-shadow: var(--theme-shadow);
            border-radius: var(--theme-border-radius);
            border: var(--theme-border);
            font-size: 16px;
            line-height: 1.6;
            display: none;
            margin-bottom: 40px;
        }
        .tab-content.active {
            display: block;
        }
        /* AMENITIES - Styled as Badge List */
        .amenities-list {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 0;
            margin: 0;
            justify-content: center;
        }
        .amenities-list li {
            background: var(--theme-secondary-bg);
            padding: 8px 16px;
            border-radius: var(--theme-border-radius);
            font-size: 14px;
            font-weight: 500;
            color: var(--theme-text-color);
            text-transform: capitalize;
        }
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .property-details-card {
                flex-direction: column;
            }
            .header-buttons {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <?php require("./navbar.php"); ?>
    <!-- MAIN CONTAINER -->
    <div class="container">
        <!-- RE-DESIGNED, INTERACTIVE PROPERTY CARD -->
        <div class="property-details-card">
            <div class="image-section">
                <img src="../assets/uploads/meal-services/<?php echo htmlspecialchars($property['main_image']); ?>" alt="Main Image">
            </div>
            <div class="info-section">
                <div class="header-text">
                    <h2><?php echo htmlspecialchars($property['name']); ?> </h2>
                    <div class="header-buttons">
                        <?php $token = base64_encode($property['service_id']);
                        echo '<a href="update-meal-services.php?token=' . urlencode($token) . '" class="call-button" >Update Details</a>'; ?>
                    </div>
                </div>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($property['category']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($property['address']); ?></p>
            </div>
        </div>
        <!-- TABS for Additional Details -->
        <div class="tabs">
            <div class="tab active" onclick="showTab('overview')">Overview</div>
            <div class="tab" onclick="showTab('amenities')">Amenities</div>
        </div>
        <div class="tab-content active" id="overview">
            <p><?php echo $property['description']; ?></p>
        </div>
        <div class="tab-content" id="amenities">
            <ul class="amenities-list">
                <?php
                foreach (explode(",", $property["amenities"]) as $amenity) {
                    $amenityArray = explode(":", $amenity) ?>
                    <li><?php echo "<i class='" . htmlspecialchars($amenityArray[1]) . "'></i> &nbsp;" . htmlspecialchars($amenityArray[0]); ?></li>
                <?php } ?>
            </ul>
        </div>
        <div class="photos">
            <div class="slider-container">
                <div class="slider">
                    <?php
                    $images = array_filter(array_map('trim', explode(",", $property["images"])));
                    foreach ($images as $image) {
                    ?>
                        <img src="../assets/uploads/meal-services/<?php echo htmlspecialchars($image); ?>" alt="Property Image">
                    <?php
                    }
                    ?>
                </div>
                <div class="slider-controls">
                    <button onclick="prevSlide()">❮</button>
                    <button onclick="nextSlide()">❯</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        let currentIndex = 0;
        const slider = document.querySelector('.slider');
        const totalSlides = document.querySelectorAll('.slider img').length;
        function showSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentIndex = index;
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
        }
        function nextSlide() {
            showSlide(currentIndex + 1);
        }
        function prevSlide() {
            showSlide(currentIndex - 1);
        }
        // Auto-advance every 5 seconds
        setInterval(nextSlide, 5000);
        // Initialize first slide
        showSlide(0);
    </script>
    <script>
                function showTab(tabId) {
                    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                }
            </script>
    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>
</body>
</html>