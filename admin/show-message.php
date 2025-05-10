<?php
    if (isset($_SESSION['success'])) {
        echo "<div class='info-container'> <p class='success'>";
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        echo " </p></div>";
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='info-container'> <p class='error'>";
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        echo " </p></div>";
    }
    ?>