<?php
session_start();
include './header.php'; // Fetch session data

$level = htmlspecialchars($level);
$Role = htmlspecialchars($role);

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Clean output buffer to avoid any unwanted output
ob_clean();

include_once '../connectDB/connectDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    // Log errors and return a generic error message
    error_log(print_r(sqlsrv_errors(), true)); 
    die(json_encode(['error' => 'Database connection failed.']));
}

// Default usrid list
$usrid_default = ['16387'];

// Use match to assign additional usrid based on level and role
$usrid = array_merge($usrid_default, match (true) {
    $level == '3' => ['36', '42', '47', '50', '79', '80', '96', '97', '101', 
                    '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
                    '127', '128', '129', '131', '132', '133', '135', '140', '150'],
    $level == '2' && ($Role == 'MK' || $Role == 'SUPER ADMIN') => ['23', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
              '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
              '127', '128', '129', '131', '132', '133', '135', '140', '150'],
    $level == '2' && $Role == 'MK Online' => ['23', '25','26','36', '42', '47', '50', '79', '80', '89', 
              '96', '97', '101', '104', '105', '107', '110', '112', '115', '122', 
              '124', '125', '126', '127', '128', '129', '131', '132', '133', '135', 
              '140', '150'],
    $level == '2' && $Role == 'MK Offline' => ['23', '25', '26', '30', '36', '42', '47', '50', '79', '80', 
              '89', '93', '96', '97', '101', '104', '105', '107', '110', '112', 
              '115', '118','122', '124', '125', '126', '127', '128', '129', '131', 
              '132', '133', '135', '137', '138', '140', '150', '152'],
    default => []
});

// Check if usrid is empty
if (empty($usrid)) {
    echo json_encode(['error' => 'No valid users found']);
    exit;
}

// Prepare the query
$placeholders = implode(',', array_fill(0, count($usrid), '?'));
$sql = "SELECT A.staff_id, B.fname_e, B.nick_name 
        FROM xuser AS A
        LEFT JOIN hr_staff B ON A.staff_id = B.staff_id
        WHERE gid = '16387' 
          AND usrid NOT IN ($placeholders)
          AND isactive = 'Y' 
          AND A.staff_id <> ''";

$stmt1 = sqlsrv_query($objCon, $sql, $usrid);

if ($stmt1 === false) {
    // Log SQL errors and return a generic error message
    error_log(print_r(sqlsrv_errors(), true)); 
    die(json_encode(['error' => 'Query execution failed.']));
}

// Fetch the data and store it in an array
$sales_data = array();
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $sales_data[] = $row;
}

// Close the connection
sqlsrv_close($objCon);

// Ensure only JSON is sent as output
header('Content-Type: application/json');
echo json_encode($sales_data);

