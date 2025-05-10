<?php
require("./check.php");
$id = $_SESSION["user_id"];
$query = "SELECT * FROM laundry_services gs left join addresses ad ON  gs.address_id=ad.address_id WHERE gs.user_id='$id'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Laundry Services </title>

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
        <h2>Your laundry</h2>

        <?php require("./show-message.php"); ?>
        <div class="listing-container">

            <?php if (mysqli_num_rows($result) > 0) {
                
                echo '<a href="./add-laundry-services.php" class="btn absolute-btn">Add New</a>';
                while ($listing = mysqli_fetch_assoc($result)) { ?>
                    <div class="property-card">
                        <button class="delete-post-btn" data-id="<?= base64_encode($listing['service_id']); ?>">✕</button>

                        <img src="<?php echo "../assets/uploads/laundry-services/" . $listing['main_image']; ?>" class="property-image"
                            alt="Laundry Image">
                        <span class="tag"><?php echo $listing['city']; ?></span>
                        <h3 class="address"><?php echo $listing['name'];?></h3>
                        <?php $token = base64_encode($listing['service_id']);
                        echo '<a href="laundry-services-details.php?token=' . urlencode($token) . '" class="contact-btn" >View Details</a>'; ?>

                    </div>
                <?php }
            } else { ?>
                <div class="empty-state">
                    <div class="empty-state__content">
                        <div class="empty-state__icon">
                            <img src="../assets/images/pencil.png" alt="">
                        </div>
                        <div class="empty-state__message">You Don't have any laundry Listed here.</div>
                        <div class="empty-state__help">
                            <a href="./add-laundry-services.php" class="btn">Add your laundry</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".delete-post-btn").forEach(button => {
                button.addEventListener("click", function() {
                    let postId = this.getAttribute("data-id");

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to recover this property!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("delete-laundry-post.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: "laundry_id=" + encodeURIComponent(postId)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: "Deleted!",
                                            text: "The laundry has been removed.",
                                            icon: "success",
                                            confirmButtonColor: "#3085d6"
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire("Error!", data.error, "error");
                                    }
                                })
                                .catch(error => {
                                    console.error("Error:", error);
                                    Swal.fire("Error!", "Something went wrong. Please try again.", "error");
                                });
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>