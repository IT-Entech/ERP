<?php
session_start();
// Assuming the user is logged in and session variables are set
$name = isset($_SESSION["name"]) ? $_SESSION["name"] : "Guest";
$username = isset($_SESSION["Username"]) ? $_SESSION["Username"] : "Guest";
$staff = isset($_SESSION["staff_id"]) ? $_SESSION["staff_id"] : "Guest";
$level = isset($_SESSION["level"]) ? $_SESSION["level"] : "0";
$role = isset($_SESSION["role"]) ? $_SESSION["role"] : "Unknown";
?>
