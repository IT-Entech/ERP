<?php
session_start();
function fetchHead()
{
    $response = [
        "name"     => $_SESSION["name"] ?? "Guest",
        "username" => $_SESSION["Username"] ?? "Guest",
        "staff"    => $_SESSION["staff_id"] ?? "0",
        "level"    => $_SESSION["level"] ?? "0",
        "role"     => $_SESSION["role"] ?? "Unknown",
        "position"     => $_SESSION["position"] ?? "Unknown",
    ];

    // Return the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
// Check if the function should be executed
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    fetchHead();
}
