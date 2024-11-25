<?php
session_start();
include_once '../connectDB/connectDB.php';
$objCon = connectDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // Raw password, will be hashed for comparison

    date_default_timezone_set("Asia/Bangkok");
    $timestamp = date('Y-m-d H:i:s');

    // Modify the query to select staff_id, level, and hashed password
    $strSQL = "SELECT * FROM a_user WHERE username=?";
    $parameters = [$username];
    $objQuery = sqlsrv_query($objCon, $strSQL, $parameters);
    
    if ($objQuery === false) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        sqlsrv_close($objCon);
        exit;
    }

    $objResult = sqlsrv_fetch_array($objQuery, SQLSRV_FETCH_ASSOC);
    
    if ($objResult && password_verify($password, $objResult["password"])) {
        // Password is correct
        $name = $objResult["Name"];
        $username = $objResult["username"];
        $id = $objResult["staff_id"];
        $level = $objResult["level"];
        $role = $objResult["Role"];
        $_SESSION["name"] = $name;
        $_SESSION["Username"] = $username;
        $_SESSION["staff_id"] = $id;
        $_SESSION["level"] = $level;
        $_SESSION["role"] = $role;

        // Update the user's last login time
        $SQL = "SELECT b.position_name FROM hr_staff a
        LEFT JOIN ms_position b ON a.position_code = b.position_code
        WHERE staff_id = ?";
        $param = [$id];
        $Query = sqlsrv_query($objCon, $SQL, $param);
        $Result = sqlsrv_fetch_array($Query, SQLSRV_FETCH_ASSOC);
        $position = $Result["position_name"];
        $_SESSION["position"] = $position;
        // Update the user's last login time
        $strSQL = "UPDATE a_user SET update_time = ? WHERE staff_id = ?";
        $param = [$timestamp, $id];
        $objQuery = sqlsrv_query($objCon, $strSQL, $param);
        
        if ($objQuery === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update login time']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Login Successful!', 'level' => $level]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username or Password is incorrect!']);
    }

    sqlsrv_close($objCon);
}
?>
