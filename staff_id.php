<?php
session_start();
include './header.php'; // Fetch session data

$level = isset($_SESSION['level']) ? htmlspecialchars($_SESSION['level']) : null;
$role = isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : null;
$staff = isset($_SESSION["staff_id"]) ? $_SESSION["staff_id"] : null;
echo $staff;
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Clean output buffer to avoid any unwanted output
ob_clean();

include_once '../connectDB/connectDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    // Log errors and return a generic error message
    error_log("Database connection failed: " . print_r(sqlsrv_errors(), true)); 
    die(json_encode(['error' => 'Database connection failed.']));
}


// Default usrid list
$usrid_default = ['16387'];

// Ensure the PHP version supports `match`, otherwise use an if-else block
if (PHP_VERSION_ID >= 80000) {
    // Use match for PHP 8.0+
    $usrid = array_merge($usrid_default, match (true) {
        $level == '3' => ['36', '42', '47', '50', '79', '80', '96', '97', '101', 
                          '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
                          '127', '128', '129', '131', '132', '133', '135', '140', '150'],
        $level == '2' && ($role == 'MK' || $role == 'SUPER ADMIN') => ['23', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
                      '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
                      '127', '128', '129', '131', '132', '133', '135', '140', '150'],
        $level == '2' && $role == 'MK Online' => ['23', '25','26','36', '42', '47', '50', '79', '80', '89', 
                      '96', '97', '101', '104', '105', '107', '110', '112', '115', '122', 
                      '124', '125', '126', '127', '128', '129', '131', '132', '133', '135', 
                      '140', '150'],
        $level == '2' && $role == 'MK Offline' => ['23', '25', '26', '30', '36', '42', '47', '50', '79', '80', 
                      '93', '96', '97', '101', '104', '105', '107', '110', '112', 
                      '115', '118','122', '124', '125', '126', '127', '128', '129', '131', 
                      '132', '133', '135', '137', '138', '140', '150', '152'],
        default => []
    });
} else {
    // For PHP < 8.0, use if-else
    $usrid = $usrid_default;
    if ($level == '3') {
        $usrid = array_merge($usrid, ['36', '42', '47', '50', '79', '80', '96', '97', '101', 
                          '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
                          '127', '128', '129', '131', '132', '133', '135', '140', '150']);
    } elseif ($level == '2' && ($role == 'MK' || $role == 'SUPER ADMIN')) {
        $usrid = array_merge($usrid, ['23', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
                      '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
                      '127', '128', '129', '131', '132', '133', '135', '140', '150']);
    } elseif ($level == '2' && $role == 'MK Online') {
        $usrid = array_merge($usrid, ['23', '25','26','36', '42', '47', '50', '79', '80', '89', 
                      '96', '97', '101', '104', '105', '107', '110', '112', '115', '122', 
                      '124', '125', '126', '127', '128', '129', '131', '132', '133', '135', 
                      '140', '150']);
    } elseif ($level == '2' && $role == 'MK Offline') {
        $usrid = array_merge($usrid, ['23', '25', '26', '30', '36', '42', '47', '50', '79', '80', 
                      '93', '96', '97', '101', '104', '105', '107', '110', '112', 
                      '115', '118','122', '124', '125', '126', '127', '128', '129', '131', 
                      '132', '133', '135', '137', '138', '140', '150', '152']);
    }
}

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
          AND A.isactive = 'Y' 
          AND A.staff_id <> ''";

$stmt1 = sqlsrv_query($objCon, $sql, $usrid);

if ($stmt1 === false) {
    // Log SQL errors and return a generic error message
    error_log("SQL query failed: " . print_r(sqlsrv_errors(), true)); 
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

