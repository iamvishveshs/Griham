<?php
require_once("../database.php");
header('Content-Type: application/json');
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
    $query = "SELECT amenity_id, amenity_name FROM amenities WHERE amenity_name LIKE ?";
    $stmt = mysqli_prepare($conn, $query);
    $likeSearch = "%".$search."%";
    mysqli_stmt_bind_param($stmt, "s", $likeSearch);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $amenities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $amenities[] = [
            "amenity_id" => $row['amenity_id'],
            "amenity_name" => $row['amenity_name']
        ];
    }
    echo json_encode($amenities);
} else {
    echo json_encode([]);
}
?>
