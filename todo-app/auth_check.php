<?php

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
   
    header("Location: login.php");
    exit; 
}


$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];
?>