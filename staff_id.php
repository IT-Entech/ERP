<?php
include './header.php';
$level = htmlspecialchars($level);
$Role = htmlspecialchars($role);

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../connectDB/connectDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(print_r(sqlsrv_errors(), true));
}
if($level == '3'){
    $usrid = ['16387', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
    '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
    '127', '128', '129', '131', '132', '133', '135', '140', '150'];
}elseif($level = '2' && $Role == 'MK' || $Role == 'SUPER ADMIN'){
    $usrid = ['16387', '23', '36', '42', '47', '50', '79', '80', '96', '97', '101', 
    '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
    '127', '128', '129', '131', '132', '133', '135', '140', '150'];
}elseif($level = '2' && $Role == 'MK Online'){
    $usrid = ['16387', '23', '25','26','36', '42', '47', '50', '79', '80', '89','96', '97', '101', 
    '104', '105', '107', '110', '112', '115', '122', '124', '125', '126', 
    '127', '128', '129', '131', '132', '133', '135', '140', '150'];
}elseif($level = '2' && $Role == 'MK Offline'){
    $usrid = ['16387', '23', '25', '26', '30', '36', '42', '47', '50', '79', '80', '89' , '93', '96', '97', '101', 
    '104', '105', '107', '110', '112', '115', '118','122', '124', '125', '126', 
    '127', '128', '129', '131', '132', '133', '135', '137', '138', '140', '150', '152'];
}
$placeholders = implode(',', array_fill(0, count($usrid), '?'));
$sql = "SELECT A.staff_id, B.fname_e, B.nick_name 
            FROM xuser AS A
            LEFT JOIN hr_staff B ON A.staff_id = B.staff_id
            WHERE gid = '16387' 
              AND usrid NOT IN ($placeholders)
              AND isactive = 'Y' 
              AND A.staff_id <> ''";
$stmt1 = sqlsrv_query($objCon, $sql, $usrid);
$sales_data = array();
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $sales_data[] = $row;
}
sqlsrv_close($objCon);
header('Content-Type: application/json');
echo json_encode($sales_data);
?>