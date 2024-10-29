<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('C:\xampp\htdocs\connectDB\connectDB.php');
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}


    $sql = "SELECT id,staff_id,Name,level,Role,active FROM a_user";


$stmt = sqlsrv_query($objCon, $sql);

if ($stmt === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

$permission = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $permission[] = $row;
}
    $data = [
        "permission" => $permission
];


sqlsrv_free_stmt($stmt);
sqlsrv_close($objCon);

header('Content-Type: application/json');
echo json_encode($data);
?>
