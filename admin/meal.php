<?php
require("./check.php");

// ✅ SQL Query with Joins & Filters
$query = "SELECT 
            ms.*,
            adr.city, 
            adr.tehsil, 
            adr.district, 
            adr.state, 
            CONCAT_WS(', ', NULLIF(adr.village, ''), NULLIF(adr.po, ''), adr.tehsil, adr.district, adr.state, NULLIF(adr.pincode, '')) AS full_address
          FROM meal_services ms
          LEFT JOIN addresses adr ON ms.address_id = adr.address_id GROUP BY ms.service_id, adr.address_id";




if ($result = mysqli_query($conn, $query)) {

    $total_results = mysqli_num_rows($result);
} else {
    $_SESSION['error'] = "Some Error Occured. Try again Later.";
    header("Location: ./home.php");
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Listings</title>
    <?php require("./style-files.php"); ?>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <style>
        .full-container{
            justify-content: center;
            align-items: center;
            
        }
    </style>
</head>

<body>

    <?php require("./navbar.php"); ?>

    <div class="full-container-c">
    <div class="section-header">
            <div>
                <h2>Meal Services</h2>
            </div>
        </div>
    <div class="table">
        <table id="myTable" class="display">
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
                <?php if ($total_results > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {

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
                    echo " <td colspan='6'>No results.</td>";
                } ?>
            </tbody>
        </table>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <?php if ($total_results > 0) { ?>
    <script>
        let table = new DataTable('#myTable', {
            select: true,
            fixedHeader: true,
            responsive: true
        });
    </script>
    
    <?php } ?>
    <?php require("./footer.php"); ?>
</body>

</html>