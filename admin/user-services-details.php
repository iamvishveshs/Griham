<?php
require("./check.php");

if (isset($_GET['token'])) {
    $user_detail_id = base64_decode($_GET['token'], true);

    if ($user_detail_id === false || !ctype_digit($user_detail_id)) {
        die("Invalid request.");
    }

    $accomodation_query = "SELECT 
            ms.*,
            adr.city, 
            adr.tehsil, 
            adr.district, 
            adr.state, 
            CONCAT_WS(', ', NULLIF(adr.village, ''), NULLIF(adr.po, ''), adr.tehsil, adr.district, adr.state, NULLIF(adr.pincode, '')) AS full_address
          FROM accommodation_services ms
          LEFT JOIN addresses adr ON ms.address_id = adr.address_id  Where user_id='$user_detail_id'GROUP BY ms.service_id, adr.address_id";
    $accomodation_result = mysqli_query($conn, $accomodation_query);

    $meal_query = "SELECT 
   ms.*,
   adr.city, 
   adr.tehsil, 
   adr.district, 
   adr.state, 
   CONCAT_WS(', ', NULLIF(adr.village, ''), NULLIF(adr.po, ''), adr.tehsil, adr.district, adr.state, NULLIF(adr.pincode, '')) AS full_address
 FROM meal_services ms
 LEFT JOIN addresses adr ON ms.address_id = adr.address_id WHERE ms.user_id='$user_detail_id'GROUP BY ms.service_id, adr.address_id";
    $meal_result = mysqli_query($conn, $meal_query);


    $gym_query = "SELECT 
ms.*,
adr.city, 
adr.tehsil, 
adr.district, 
adr.state, 
CONCAT_WS(', ', NULLIF(adr.village, ''), NULLIF(adr.po, ''), adr.tehsil, adr.district, adr.state, NULLIF(adr.pincode, '')) AS full_address
FROM gym_services ms
LEFT JOIN addresses adr ON ms.address_id = adr.address_id WHERE ms.user_id='$user_detail_id'GROUP BY ms.service_id, adr.address_id";
    $gym_result = mysqli_query($conn, $gym_query);
} else {
    die("Access Denied");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Listings</title>
    <?php require("./style-files.php"); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <style>
        .full-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap:50px;
        }
    </style>
</head>

<body>

    <?php require("./navbar.php"); ?>

    <div class="full-container-c">
        <div class="section-header margin-top">
            <div>
                <h2>Accomodation Services</h2>
            </div>
        </div>
        <div class="table">
            <table id="accommodationTable" class="display">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($accomodation_result) > 0) {
                        while ($row = mysqli_fetch_assoc($accomodation_result)) {

                    ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['property_name']); ?> </td>
                                <td><?php echo $row['property_type']; ?></td>
                                <td><?php echo $row['contact_number']; ?></td>
                                <td> <?php echo htmlspecialchars($row['city'] . " - " . $row['state']); ?></td>
                                <?php $token = base64_encode($row['service_id']);
                                echo '<td><a href="property-details.php?token=' . urlencode($token) . '" class="custom-button" ><i class="fa fa-eye"></i> View Details</a></td>'; ?>
                            </tr>
                    <?php
                        }
                    } else {
                        echo " <tr class='center'><td colspan='5'>No results.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
        <div class="section-header margin-top">
            <div>
                <h2>Meal Services</h2>
            </div>
        </div>
        <div class="table">
            <table id="mealTable" class="display">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Category</th>
                        <th>Timing</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($meal_result) > 0) {
                        while ($row = mysqli_fetch_assoc($meal_result)) {

                    ?>

                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?> </td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo date('g:i A', strtotime(htmlspecialchars($row['opening_hours']))) . " - " . date('g:i A', strtotime(htmlspecialchars($row['closing_hours']))); ?></td>
                                <td><?php echo $row['contact_number']; ?></td>
                                <td> <?php echo htmlspecialchars($row['city'] . " - " . $row['state']); ?></td>
                                <?php $token = base64_encode($row['service_id']);
                                echo '<td><a href="meal-services-details.php?token=' . urlencode($token) . '" class="custom-button" ><i class="fa fa-eye"></i> View Details</a></td>'; ?>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr class='center'><td colspan='5'>No results.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
        <div class="section-header margin-top">
            <div>
                <h2>Gym Services</h2>
            </div>
        </div>
        <div class="table">
            <table id="gymTable" class="display">
                <thead>
                    <tr>
                        <th>Gym Name</th>
                        <th>Timing</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($gym_result) > 0) {
                        while ($row = mysqli_fetch_assoc($gym_result)) {

                    ?>

                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?> </td>
                                <td><?php echo date('g:i A', strtotime(htmlspecialchars($row['opening_hours']))) . " - " . date('g:i A', strtotime(htmlspecialchars($row['closing_hours']))); ?></td>
                                <td><?php echo $row['contact_number']; ?></td>
                                <td> <?php echo htmlspecialchars($row['city'] . " - " . $row['state']); ?></td>
                                <?php $token = base64_encode($row['service_id']);
                                echo '<td><a href="gym-services-details.php?token=' . urlencode($token) . '" class="custom-button" ><i class="fa fa-eye"></i> View Details</a></td>'; ?>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr class='center'><td colspan='5'>No results.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script>

        $(document).ready(function() {
            <?php if (mysqli_num_rows($accomodation_result) > 0) { ?>
            $('#accommodationTable').DataTable({
                responsive: {
                    details: false
                }
            });
            <?php } if (mysqli_num_rows($meal_result) > 0) { ?>
            $('#mealTable').DataTable({
                responsive: {
                    details: false
                }
            });
            <?php } if (mysqli_num_rows($gym_result) > 0) { ?>
            $('#gymTable').DataTable({
                responsive: {
                    details: false
                }
            });
            <?php }  ?>
        });
    </script>
    <?php require("./footer.php"); ?>
</body>

</html>