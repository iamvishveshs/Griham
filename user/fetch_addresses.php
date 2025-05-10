<?php
require("./check.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['query'])) {
    $query = mysqli_real_escape_string($conn, trim($_POST['query']));
    $sql = "SELECT DISTINCT city, district, tehsil, state FROM addresses WHERE city LIKE '%$query%' OR district LIKE '%$query%' OR tehsil LIKE '%$query%' LIMIT 10;";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li class='suggestion-item'>{$row['city']} , {$row['state']}</li>";
        }
    } else {
        echo "";
    }
}