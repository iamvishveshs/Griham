<?php
require("./check.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['query'])) {
    echo "";
    exit();
}

$query = mysqli_real_escape_string($conn, trim($_POST['query']));
$sql = "SELECT * FROM addresses WHERE village LIKE '%$query%' OR city LIKE '%$query%' OR district LIKE '%$query%'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='address-suggestion-item' data-address-id='" . $row['address_id'] . "' 
              data-full-address='" . htmlspecialchars($row['city'] . ", " . $row['district']) . "' 
              data-city='" . htmlspecialchars($row['city']) . "' 
              data-village='" . htmlspecialchars($row['village']) . "' 
              data-po='" . htmlspecialchars($row['po']) . "' 
              data-tehsil='" . htmlspecialchars($row['tehsil']) . "' 
              data-district='" . htmlspecialchars($row['district']) . "' 
              data-state='" . htmlspecialchars($row['state']) . "' 
              data-pincode='" . htmlspecialchars($row['pincode']) . "'>" . 
              htmlspecialchars($row['village']).", ".
              htmlspecialchars($row['city']) . ", " . htmlspecialchars($row['district']) . "</div>";
    }
} else {
    echo "<div class='suggestion'>No matching address found. Please enter manually.</div>";
}
?>
