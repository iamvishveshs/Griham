<?php
require("./check.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data to prevent SQL injection
    function clean_input($data, $conn)
    {
        $data = trim($data); // Remove extra spaces
        $data = strtolower($data); // Convert to lowercase for consistency
        $data = ucwords($data); // Capitalize first letter of each word
        return mysqli_real_escape_string($conn, htmlspecialchars($data));
    }


    try {


        // Personal Details
        $user_id = $_SESSION['user_id'];

        // Image Upload Directory
        $upload_dir = "../assets/uploads/profile/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $main_image_path = null; // Store new image name if uploaded
        $image_update_query = ""; // To store dynamic SQL update
        $old_image_path = null; // Store previous image path

        // Fetch the old profile picture filename before updating
        $query = "SELECT profile_pic FROM users WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $old_image_path = $row['profile_pic']; // Store old image filename
        }

        if (isset($_FILES['main_image']['name']) && !empty($_FILES['main_image']['name'])) {
            if ($_FILES['main_image']['error'] === 0) {  // Check if file uploaded successfully
                $main_image_name = basename($_FILES['main_image']['name']);
                $main_image_ext = strtolower(pathinfo($main_image_name, PATHINFO_EXTENSION));

                if (in_array($main_image_ext, ["jpg", "jpeg", "png", "gif", "webp"])) {
                    $new_main_filename = "main_" . uniqid() . "." . $main_image_ext;
                    $main_image_path = $upload_dir . $new_main_filename;

                    if (move_uploaded_file($_FILES['main_image']['tmp_name'], $main_image_path)) {
                        $main_image_path = $new_main_filename; // Store only filename in DB
                        $image_update_query = "UPDATE `users` SET `profile_pic`='$main_image_path' WHERE user_id='$user_id'"; // SQL update string
                        if (mysqli_query($conn, $image_update_query)) {
                            if ($old_image_path && file_exists($upload_dir . $old_image_path)) {
                                unlink($upload_dir . $old_image_path);
                                
                            }
                            $_SESSION['success'] = "Details saved successfully!";
                            header("Location: ./account.php");
                            exit();
                        }
                        // ✅ Delete old image if it exists
                        
                    } else {
                        throw new Exception("Error in Image Upload");
                    }
                } else {
                    throw new Exception("Invalid Image format");
                }
            } else {
                throw new Exception("File upload error: " . $_FILES['main_image']['error']);
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ./account.php");
        exit();
    }
}

// Get user_id from session
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;



if ($user_id > 0) {
    // Fetch personal details
    $query = "SELECT * from users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $full_name = $row['name'] ?? "";

        $email = $row['email'] ?? "";
        $phone = $row['phone'] ?? "";
        $_SESSION['nav_profile_pic'] = $row['profile_pic'] ?? "";
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Details</title>

    <?php
    require("./style-files.php");
    ?>
    <link rel="stylesheet" href="../assets/css/form.css">
    <style>
        #imagePreviewMain img {
            width: 100px;
            height: 100px;
            margin: 5px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <?php require("./navbar.php"); ?>

    <h2 style="text-align: center;margin-top:20px">Account Details</h2>
    <?php require("./show-message.php") ?>
    <form action="./account.php" method="POST" class="form-container" enctype="multipart/form-data">

        <div class="form-group" style="min-width:100%;display:flex;justify-content:center;align-items:center;flex-direction:column;">
            <div style="max-width: 250px;text-align:center;" id="imagePreviewMain" class="preview-container">
                <?php
                if ($_SESSION['nav_profile_pic'] != "") {
                    echo "<img src='../assets/uploads/profile/" . $_SESSION['nav_profile_pic'] . "' alt='Profile Picture'/>";
                } else {
                    echo "<img src='../assets/uploads/profile/user.png' alt='Profile Picture'/>";
                }
                ?>
            </div>
            <div style="max-width: 250px; max-height:100px;">
                <label class="custum-file-upload" for="imageUploadMain">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 24 24">
                            <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                            <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill=""
                                    d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z"
                                    clip-rule="evenodd" fill-rule="evenodd"></path>
                            </g>
                        </svg>
                    </div>
                    <div class="text">
                        <span>Your Photograph</span>
                    </div>

                    <input type="file" id="imageUploadMain" name="main_image"
                        accept="image/png, image/jpeg,image/webp, image/gif">
                </label>

            </div>


        </div>


        <h3>Personal Details</h3>
        <div class="form-group">
            <div>
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" placeholder="Enter full name" disabled>
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" disabled>
            </div>
            <div>
                <label>Mobile Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Enter mobile number" disabled>
            </div>


        </div>


    </form>



    <?php require("./footer.php"); ?>
    <script>
        document.getElementById("imageUploadMain").addEventListener("change", function(event) {
            let previewContainer = document.getElementById("imagePreviewMain");
            previewContainer.innerHTML = ""; // Clear previous previews

            let files = event.target.files;
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    let fileType = file.type;

                    // Check if file is a valid image type
                    if (fileType.match("image.*")) {
                        let reader = new FileReader();

                        reader.onload = function(e) {
                            let imgElement = document.createElement("img");
                            imgElement.src = e.target.result;
                            imgElement.style.width = "100px";
                            imgElement.style.height = "100px";
                            imgElement.style.margin = "5px";
                            imgElement.style.borderRadius = "50%";
                            imgElement.style.boxShadow = "0 2px 5px rgba(0,0,0,0.2)";
                            previewContainer.appendChild(imgElement);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let form = document.querySelector(".form-container");
            let originalValues = {};
            let saveButton = null;

            // Store initial values of all input and select fields
            form.querySelectorAll("input, select").forEach(field => {
                originalValues[field.name] = field.value;
            });

            function checkForChanges() {
                let hasChanges = false;

                // Compare current values with the original ones
                form.querySelectorAll("input, select").forEach(field => {
                    if (originalValues[field.name] !== field.value) {
                        hasChanges = true;
                    }
                });

                if (hasChanges) {
                    addSaveButton();
                } else {
                    removeSaveButton();
                }
            }

            function addSaveButton() {
                if (!saveButton) {
                    saveButton = document.createElement("button");
                    saveButton.className = "btn";
                    saveButton.type = "submit";
                    saveButton.textContent = "Save ➢";
                    form.appendChild(saveButton);
                }
            }

            function removeSaveButton() {
                if (saveButton) {
                    saveButton.remove();
                    saveButton = null;
                }
            }

            // Listen for changes on input and select fields
            form.querySelectorAll("input, select").forEach(field => {
                field.addEventListener("input", checkForChanges);
                field.addEventListener("change", checkForChanges);
            });
        });
    </script>

    <?php
    require("./script-files.php");
    ?>
</body>

</html>