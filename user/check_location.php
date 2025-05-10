<?php
if(!isset($_SESSION['user_choice_location']))
{
    header('Location: ./location.php');
}
?>