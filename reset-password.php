<?php
include_once '../connectDB/connectDB.php';
$objCon = connectDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $Newpassword = $_POST['newPassword']; 
    $Confirmpassword = $_POST['confirmPassword'];
 // Check if New password and Confirm password match
 if ($Newpassword !== $Confirmpassword) {
    echo json_encode(['status' => 'error', 'message' => 'New password and Confirm password do not match!']);
    exit;
}
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
    
    if ($objResult) {
    
        $hashedPassword = password_hash($Newpassword, PASSWORD_DEFAULT);
        // Update the user's password
        $strSQL = "UPDATE a_user SET password = ?, update_time = ? WHERE username = ?";
        $param = [$hashedPassword, $timestamp, $username];
        $objQuery = sqlsrv_query($objCon, $strSQL, $param);
        
        if ($objQuery === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Password reset successful!', 'level' => $objResult['level']]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }

    sqlsrv_close($objCon);
}
?>
