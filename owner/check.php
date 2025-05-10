<?php
session_start();

require("database.php");
/*Checking if there is any user session if no then redirected to login page*/
if(!isset($_SESSION["user_id"]) || $_SESSION['role']!="owner")
{
    header("Location:../login.php");
}
?>