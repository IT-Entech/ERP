<?php
include_once '../../connectDB/connectDB.php';
$objCon = connectDB();
/*
$sales = isset($_GET['channel']) ? $_GET['channel'] : NULL;
$uid = "SELECT * FROM xuser WHERE staff_id LIKE '%$sales%'";
$uid = sqlsrv_query($objCon, $uid);
$uid = sqlsrv_fetch_array($uid, SQLSRV_FETCH_ASSOC);
$uid = $uid['usrid'];
*/
$timezone = new DateTimeZone('Asia/Bangkok'); // You can use 'Asia/Bangkok', 'Asia/Jakarta', etc.

// Create a DateTime object with the specified time zone
$date = new DateTime('now', $timezone);
$record_datetime = $date->format('Y-m-d H:i:s'); // For date and time in YYYY-MM-DD HH:MM:SS format

$data = $_POST;

//print_r($data);
$id_no_count = count(array_filter(array_keys($data), function($key) {
    return strpos($key, 'id') === 0;
}));
for ($i = 1; $i <= $id_no_count; $i++) {
    $id = $data["id$i"];
    $active = $data["active$i"];
    $level = $data["level$i"];
    $role = $data["Role$i"];

    // SQL query with parameters
    $sql = "UPDATE a_user SET
            level = ?,
            Role = ?,
            active = ?
            WHERE id = ?";
    
    // Parameters for the query
    $params = array($level, $role, $active, $id);
    
    // Execute the query
    $stmt = sqlsrv_query($objCon, $sql, $params);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
       
        echo '<script>alert("แก้ไขสิทธิ์แล้ว");window.location="index.html";</script>';
     
        }
}

sqlsrv_close($objCon);
?>
