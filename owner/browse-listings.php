<?php
require("./check.php");
$user_id = $_SESSION['user_id']; // Get logged-in user ID

$query = "SELECT 
            a.service_id, a.property_name, a.property_type, a.contact_number, a.description, a.main_image,
            adr.city, adr.village, adr.po, adr.tehsil, adr.district, adr.state, adr.pincode, adr.latitude, adr.longitude,
            GROUP_CONCAT(DISTINCT am.amenity_name ORDER BY am.amenity_name SEPARATOR ', ') AS amenities,
            GROUP_CONCAT(DISTINCT img.image_url ORDER BY img.image_id SEPARATOR ', ') AS images
          FROM accommodation_services a
          LEFT JOIN addresses adr ON a.address_id = adr.address_id
          LEFT JOIN accommodation_amenities aa ON a.service_id = aa.service_id
          LEFT JOIN amenities am ON aa.amenity_id = am.amenity_id
          LEFT JOIN images img ON a.service_id = img.entity_id AND img.entity_type = 'accommodation_service'
          WHERE a.user_id = '$user_id'
          GROUP BY a.service_id";

$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Rooms </title>

    <?php
    require("./style-files.php");
    ?>
    <style>
        .property-card {
            position: relative;
            display: inline-block;
        }

        .delete-post-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background-color: white;
            color: red;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: background 0.3s ease;
        }

        .delete-post-btn:hover {
            background-color: red;
            color: white;
        }
    </style>

</head>

<body>
    <?php require("./navbar.php"); ?>
    <div class="page-container">
        <h2>Your Property Listings</h2>

        <?php require("./show-message.php"); ?>
        <div class="listing-container">

            <?php if (mysqli_num_rows($result) > 0) {

                echo '<a href="./add-listing.php" class="btn absolute-btn">Add New</a>';
                while ($listing = mysqli_fetch_assoc($result)) { ?>
                    <div class="property-card">
                        <button class="delete-post-btn" data-id="<?= base64_encode($listing['service_id']); ?>">✕</button>

                        <img src="<?php echo "../assets/uploads/accommodation-images/" . $listing['main_image']; ?>" class="property-image"
                            alt="Property Image">
                        <span class="tag"><?php echo $listing['city']; ?></span>
                        <h3 class="address"><?php echo $listing['property_name']; ?></h3>
                        <?php $token = base64_encode($listing['service_id']);
                        echo '<a href="property-details.php?token=' . urlencode($token) . '" class="contact-btn" >View Details</a>'; ?>


                    </div>
                <?php }
            } else { ?>
                <div class="empty-state">
                    <div class="empty-state__content">
                        <div class="empty-state__icon">
                            <img src="../assets/images/pencil.png" alt="">
                        </div>
                        <div class="empty-state__message">You Don't have any PG/Room/Apartment Listed here.</div>
                        <div class="empty-state__help">
                            <a href="././add-listing.php" class="btn">Add your PG/Dhaba/Cafe</a>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>

    <?php require("./footer.php"); ?>
    <?php
    require("./script-files.php");
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".delete-post-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let postId = this.getAttribute("data-id");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "This action cannot be undone!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("delete-post.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: "id=" + encodeURIComponent(postId)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: "Deleted!",
                                            text: "Property deleted successfully.",
                                            icon: "success",
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire("Error!", data.error, "error");
                                    }
                                })
                                .catch(error => {
                                    console.error("Error:", error);
                                    Swal.fire("Error!", "Something went wrong.", "error");
                                });
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>