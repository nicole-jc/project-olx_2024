<?php 

session_start(); 

// verify if user is logged
if(!isset($_SESSION["user-id"]) || !isset($_SESSION["user-name"])) 
{ 
// redirect to login home
header("Location: login.php"); 
exit; 
} 