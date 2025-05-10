<?php
if (!isset($_GET['location'])) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Warning",
                    html: "<b>You cannot directly access this page.</b><br>Continue from the dashboard again.",
                    icon: "warning"
                }).then(() => {
                    window.location.href = "./home.php";
                });
            });
          </script>';
    exit();
}
?>