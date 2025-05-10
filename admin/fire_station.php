<?php
require("./check.php");

// ✅ SQL Query with Joins & Filters
$query = "SELECT
    es.service_id,
    es.name,
    es.contact_details,
    es.opening_time,
    es.closing_time,
    es.is_24_7,
    esc.category_name,
    a.city, a.state
    FROM 
    emergency_services es
INNER JOIN 
    addresses a ON es.address_id = a.address_id
INNER JOIN 
    emergency_service_categories esc ON es.category_id = esc.category_id
WHERE 
    esc.category_name = 'Fire Brigade'";


if ($result = mysqli_query($conn, $query)) {

    $total_results = mysqli_num_rows($result);
} else {
    $_SESSION['error'] = "Some Error Occured. Try again Later.";
    header("Location: ./emergency.php");
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Station</title>
    <?php require("./style-files.php"); ?>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <style>
        .full-container{
            width:100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
 
<body>

    <?php require("./navbar.php"); ?>

    <div class="full-container-c">
    <a href="./add-emergency.php" class="btn absolute-btn">Add New</a>
    <div class="section-header">
            <div>
                <h2>Fire Station</h2>
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
                    <th>Category</th>
                    <th>24/7</th>
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
                            <td><?php echo htmlspecialchars($row['category_name']); ?> </td>
                            <td><?php if(htmlspecialchars($row['is_24_7'])==1){ echo "Yes";} else {
                                echo "No";
                            } ?> </td>
                            <td><?php if(htmlspecialchars($row['is_24_7'])==1){echo "24X7";} else {echo date('g:i A', strtotime(htmlspecialchars($row['opening_time']))) . " - " . date('g:i A', strtotime(htmlspecialchars($row['closing_time'])));} ?></td>
                            <td><?php echo $row['contact_details']; ?></td>
                            <td> <?php echo htmlspecialchars($row['city'] . " - " . $row['state']); ?></td>
                            <?php $token = base64_encode($row['service_id']);
                                echo '<td><a href="update-emergency.php?token=' . urlencode($token) . '" class="custom-button" ><i class="fa fa-edit"></i></a> <a data-id=' . urlencode($token) . '" class="custom-button delete" ><i class="fa fa-trash-can"></i></a></td>'; ?>
                                </tr>
                <?php
                    }
                } else {
                    echo " <td colspan='6' class='center'>No results</td>";
                } ?>
            </tbody>
        </table>
    </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($total_results > 0) { ?>
        <?php if ($total_results > 0) { ?>
        <script>
            // Initialize DataTables
            let table = new DataTable('#myTable', {
                select: true,
                fixedHeader: true,
                responsive: true
            });
            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                let serviceId = $(this).data('id'); // Get service_id from data-id attribute

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete-emergency.php',
                            type: 'POST',
                            data: {
                                service_id: serviceId
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'The record has been deleted.',
                                    'success'
                                );
                                // Reload the table or remove the row dynamically
                                location.reload();
                            },
                            error: function(xhr) {
                                // Handle error responses based on HTTP status code
                                if (xhr.status === 500) {
                                    Swal.fire(
                                        'Error!',
                                        'Internal server error occurred. Please try again later.',
                                        'error'
                                    );
                                } else if (xhr.status === 405) {
                                    Swal.fire(
                                        'Error!',
                                        'Invalid request method.',
                                        'error'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong. Please try again.',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });
        </script>
    <?php } ?>
    
    <?php } ?>
    <?php require("./footer.php"); ?>
</body>

</html>