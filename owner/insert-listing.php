<?php
session_start();
require("./check.php");
// Retrieve form data
$user_id = $_SESSION['user_id'];
$property_name = mysqli_real_escape_string($conn, trim($_POST['property_name']));
$property_type = mysqli_real_escape_string($conn, trim($_POST['property_type']));
$address = mysqli_real_escape_string($conn, trim($_POST['address']));
$city = mysqli_real_escape_string($conn, trim($_POST['city']));
$contact_number = mysqli_real_escape_string($conn, trim($_POST['contact_number']));
$amenities = mysqli_real_escape_string($conn, trim($_POST['amenities']));
$description = mysqli_real_escape_string($conn, trim($_POST['description']));
// Check if any field is empty
if (empty($property_name) || empty($property_type) || empty($address) || empty($city) || empty($contact_number) || empty($amenities) || empty($description)) {
    $_SESSION['error'] = "All fields are required!";
    header("Location: add-listing.php");
    exit();
}
// Validate phone number
if (!preg_match('/^[1-9][0-9]{9}$/', $contact_number)) {
    $_SESSION['error'] = "Invalid phone number format!";
    header("Location: add-listing.php");
    exit();
}
// Image Upload Directory
$upload_dir = "../assets/uploads/listing-images/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
// Handle Main Front-Facing Image Upload
$main_image_path = null;
if (isset($_FILES['main_image']['name']) && !empty($_FILES['main_image']['name'])) {
    $main_image_name = basename($_FILES['main_image']['name']);
    $main_image_ext = strtolower(pathinfo($main_image_name, PATHINFO_EXTENSION));
    if (in_array($main_image_ext, ["jpg", "jpeg", "png", "gif", "webp"])) {
        $new_main_filename = "main_" . uniqid() . "." . $main_image_ext;
        $main_image_path = $upload_dir . $new_main_filename;
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $main_image_path)) {
            $main_image_path = $new_main_filename; // Store only filename in DB
        } else {
            $_SESSION['error'] = "Failed to upload the main image!";
            header("Location: add-listing.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid main image format!";
        header("Location: add-listing.php");
        exit();
    }
}
// Handle Multiple Property Images Upload
$image_paths = [];
if (!isset($_FILES['images']['name']) || count(array_filter($_FILES['images']['name'])) == 0) {
    $_SESSION['error'] = "You must upload at least one property image!";
    header("Location: add-listing.php");
    exit();
}
foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
    $file_name = basename($_FILES['images']['name'][$key]);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (in_array($file_ext, ["jpg", "jpeg", "png", "gif", "webp"])) {
        $new_filename = uniqid() . "." . $file_ext;
        $file_path = $upload_dir . $new_filename;
        if (move_uploaded_file($tmp_name, $file_path)) {
            $image_paths[] = $new_filename;
        }
    }
}
// Convert image paths to string for database storage
$image_paths_string = !empty($image_paths) ? implode(",", $image_paths) : null;
// Ensure images are uploaded before inserting into the database
if (empty($image_paths_string)) {
    $_SESSION['error'] = "Failed to upload property images!";
    header("Location: add-listing.php");
    exit();
}
// Insert into database with the new `description` field
$query = "INSERT INTO listings (user_id, property_name, property_type, address, city, contact_number, images, main_image, description, amenities)
          VALUES ('$user_id', '$property_name', '$property_type', '$address', '$city', '$contact_number', '$image_paths_string', '$main_image_path', '$description', '$amenities')";
if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Property listing added successfully!";
    header("Location: browse-listings.php");
    exit();
} else {
    $_SESSION['error'] = "Error: " . mysqli_error($conn);
    header("Location: add-listing.php");
    exit();
}
?>
