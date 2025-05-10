<?php
require("./check.php");

// ✅ SQL Query with Joins & Filters
$query = "SELECT * from users Where role!='admin' GROUP BY user_id";




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
    <title>Users Listings</title>
    <?php require("./style-files.php"); ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <style>
        .full-container {
            width: 100%;
            display: flex;
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
                <h2>User Accounts</h2>
            </div>
        </div>
        <div class="table">
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Profiile Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_results > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {

                    ?>

                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?> </td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td> <?php echo htmlspecialchars($row['role']); ?></td>
                                <td> <?php echo htmlspecialchars($row['profile_status']); ?></td>
                                <?php if($row['role'] != "tenant") { $token = base64_encode($row['user_id']);
                                echo '<td><a href="user-services-details.php?token=' . urlencode($token) . '" class="custom-button" ><i class="fa fa-eye"></i> View Details</a></td>';} else { echo "<td>No Action </td>";} ?>
                            </tr>
                    <?php
                        }
                    } else {
                        echo " <td colspan='6'></td>";
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