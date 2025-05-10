<?php
require("./check.php");
if (isset($_GET['token'])) {
    $service_id = base64_decode($_GET['token'], true);
    if ($service_id === false || !ctype_digit($service_id)) {
        die("Invalid request.");
    }
    $result = mysqli_query($conn, "SELECT * FROM `support_requests` sr
          LEFT JOIN users u ON sr.user_id = u.user_id  WHERE ticket_id='$service_id'");
    if ($user = mysqli_fetch_assoc($result)) {
    } else {
        die("Message not found.");
    }
    mysqli_close($conn);
} else {
    die("Access Denied");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Ticket : <?php echo htmlspecialchars($user['ticket_id']); ?></title>
    <link rel="stylesheet" href="../assets/css/form.css" />
    <?php require("./style-files.php"); ?>
<body>
    <?php require("./navbar.php"); ?>
    <h2 style="text-align: center;margin-top:20px">Response to Ticket : <?php echo htmlspecialchars($user['ticket_id']); ?></h2>
    <form class="form-container">
        <div class="form-group">
            <input type="text" name="ticket_id" id="ticket_id" value="<?php echo htmlspecialchars($user['ticket_id']); ?>" hidden>
            <div>
                <label>Full Name</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <div>
                <label>Message</label>
                <textarea type="text" name="description" id="description" disabled><?php echo htmlspecialchars($user['description']); ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div>
                <label>Your Response</label>
                <textarea type="text" name="response" id="response" placeholder="Enter response"></textarea>
            </div>
        </div>
        <small id="emailMessage"></small>
    </form>
    <?php require("./footer.php"); ?>
    <?php require("./script-files.php"); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let form = document.querySelector(".form-container");
            let originalValues = {};
            let saveButton = null;
            // Store initial values of all input, select, and textarea fields
            form.querySelectorAll("input, select, textarea").forEach(field => {
                if (field.name) { // Ensure the field has a name attribute
                    originalValues[field.name] = field.value;
                }
            });
            function checkForChanges() {
                let hasChanges = false;
                // Compare current values with the original ones
                form.querySelectorAll("input, select, textarea").forEach(field => {
                    if (field.name && originalValues[field.name] !== field.value) { // Check field.name exists
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
                    saveButton.id = "sendResponse";
                    saveButton.className = "custom-button";
                    saveButton.type = "button";
                    saveButton.textContent = "Send Response";
                    form.appendChild(saveButton);
                }
            }
            function removeSaveButton() {
                if (saveButton) {
                    saveButton.remove();
                    saveButton = null;
                }
            }
            // Listen for changes on input, select, and textarea fields
            form.querySelectorAll("input, select, textarea").forEach(field => {
                field.addEventListener("input", checkForChanges);
                field.addEventListener("change", checkForChanges);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 CDN -->
    <script>
        $(document).ready(function() {
            $(document).on("click", "#sendResponse", function(e) { // Use event delegation
                e.preventDefault(); // Prevent page refresh
                let email = $("#email").val();
                let ticket_id = $("#ticket_id").val();
                let name = $("#name").val();
                let description = $("#description").val();
                let response = $("#response").val();
                Swal.fire({
                    title: "Sending...",
                    text: "Your response is being sent. Please wait.",
                    icon: "info",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "../libs/send-support-response-email.php",
                    data: {
                        address: email,
                        message_id: ticket_id,
                        name: name,
                        description: description,
                        response: response
                    },
                    success: function() {
                        Swal.fire({
                            title: "Message Sent!",
                            text: "Your response has been successfully sent.",
                            icon: "success",
                            confirmButtonText: "OK"
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: "Error",
                            text: "An error occurred while sending your response. Please try again.",
                            icon: "error",
                            confirmButtonText: "Retry"
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>