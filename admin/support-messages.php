<?php
require("./check.php");
$query = "SELECT * FROM `support_requests` sr
          LEFT JOIN users u ON sr.user_id = u.user_id  ORDER BY sr.created_at DESC";
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
    <title>Support</title>
    <?php require("./style-files.php"); ?>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <style>
    .full-container {
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
                <h2>Support Requests</h2>
            </div>
            <div>
                <?php require("./show-message.php"); ?>
            </div>
        </div>
        <div class="table">
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_results > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?> </td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td><?php echo date("d-m-y", strtotime($row['created_at'])); ?></td>
                        <td><?php
                                    $truncatedDescription = substr($row['description'], 0, 20); // Truncate to 20 characters
                                    echo $truncatedDescription . '...'; // Add ellipsis
                                    ?></td>
                        <?php $token = base64_encode($row['ticket_id']);
                                echo '<td><a href="message-details.php?token=' . urlencode($token) . '" class="custom-button" ><i class="fa fa-reply"></i> Response</a><a href="message-delete.php?token=' . urlencode($token) . '" class="custom-button delete" ><i class="fa fa-trash"></i> Delete</a></td>'; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($total_results > 0) { ?>
    <script>
    let table = new DataTable('#myTable', {
        select: true,
        fixedHeader: true,
        responsive: true
    });
    document.querySelector('.custom-button.delete').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with deletion by navigating to the URL
                window.location.href = this.href;
            }
        });
    });
    </script>
    <?php } ?>
    <?php require("./footer.php"); ?>
</body>
</html>