<?php
include_once('C:\xampp\htdocs\connectDB\connectDB.php');
$objCon = connectDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idcard = $_POST['ID-Card'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


    // Check if the staff exists in hr_staff
    $strSQL = "SELECT COUNT(staff_id) AS staff_count FROM hr_staff WHERE staff_id = ?";
    $objQuery = sqlsrv_query($objCon, $strSQL, [$idcard]);
    $Result = sqlsrv_fetch_array($objQuery, SQLSRV_FETCH_ASSOC);

    if ($Result['staff_count'] > 0) {
        // Check if the staff_id already exists in a_user
        $SQL = "SELECT COUNT(staff_id) AS user_count FROM a_user WHERE staff_id = ?";
        $Query = sqlsrv_query($objCon, $SQL, [$idcard]);
        $objResult = sqlsrv_fetch_array($Query, SQLSRV_FETCH_ASSOC);

        if ($objResult['user_count'] == 0) {
            // Insert new user into a_user table
            $strSQL = "INSERT INTO a_user ([staff_id], [Name], [username], [password]) VALUES (?, ?, ?, ?)";
            $parameters = [$idcard, $name, $username, $hashedPassword];
            $objQuery = sqlsrv_query($objCon, $strSQL, $parameters);

            if ($objQuery === false) {
                echo json_encode(['status' => 'error', 'message' => sqlsrv_errors()]);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User already registered']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Staff ID not found']);
    }
}

sqlsrv_close($objCon);
?>
