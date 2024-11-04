<?php
include_once('C:\xampp\htdocs\connectDB\connectDB.php');
$objCon = connectDB();

$timezone = new DateTimeZone('Asia/Bangkok'); // Setting the timezone
$date = new DateTime('now', $timezone);
$record_datetime = $date->format('Y-m-d H:i:s'); // Current date and time

$data = $_POST;
$sales = $data['staff'];

// Fetch the user ID associated with the staff
$uidQuery = "SELECT usrid FROM xuser WHERE staff_id LIKE ?";
$uidParams = ["%$sales%"];
$uidStmt = sqlsrv_query($objCon, $uidQuery, $uidParams);
if ($uidStmt === false || !($uid = sqlsrv_fetch_array($uidStmt, SQLSRV_FETCH_ASSOC))) {
    die(print_r(sqlsrv_errors(), true));
}
$uid = $uid['usrid'];

$qt_no_count = count(array_filter(array_keys($data), function($key) {
    return strpos($key, 'qt_no') === 0;
}));

// Loop over the qt_no entries
for ($i = 1; $i <= $qt_no_count; $i++) {
    $qt_no = $data["qt_no$i"] ?? null;
    $cs_badge = $data["cs-badge-$i"] ?? null;
    $remark = $data["remark$i"] ?? null;
    $status_badge = $data["status-badge-$i"] ?? null;
    $reason = isset($data["reason$i"]) && $data["reason$i"] !== '' ? $data["reason$i"] : NULL;

    if ($qt_no) {
        // Fetch existing data for comparison
        $sqlSelect = "SELECT is_prospect, remark, is_tracking, reasoning FROM cost_sheet_head WHERE qt_no = ?";
        $stmtSelect = sqlsrv_query($objCon, $sqlSelect, [$qt_no]);

        if ($stmtSelect === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $existingData = sqlsrv_fetch_array($stmtSelect, SQLSRV_FETCH_ASSOC);
        $dataChanged = false;

        if ($existingData) {
            $dataChanged = (
                ($cs_badge !== null && $cs_badge != $existingData['is_prospect']) ||
                ($remark !== null && $remark != $existingData['remark']) ||
                ($status_badge !== null && $status_badge != $existingData['is_tracking']) ||
                ($reason !== null && $reason != $existingData['reasoning'])
            );
        }

        // Only perform update if data has changed
        if ($dataChanged) {
            $sql = "UPDATE cost_sheet_head SET ";
            $params = [];

            if ($cs_badge !== null) {
                $sql .= "is_prospect = ?, ";
                $params[] = $cs_badge;
            }

            if ($remark !== null) {
                $sql .= "remark = ?, ";
                $params[] = $remark;
            }

            if ($status_badge !== null) {
                $sql .= "is_tracking = ?, ";
                $params[] = $status_badge;
            }

            if ($reason !== null) {
                $sql .= "reasoning = ?, ";
                $params[] = $reason;
            }

            // Always update the date and user ID
            $sql .= "update_date = ?, update_id = ? WHERE qt_no = ?";
            $params[] = $record_datetime;
            $params[] = $uid;
            $params[] = $qt_no;

            // Execute the query
            $stmt = sqlsrv_query($objCon, $sql, $params);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                echo '<script>alert("อัพเดทข้อมูลแล้ว");window.location="tables-data.php";</script>';
            }
        }
    }
}

sqlsrv_close($objCon);
?>
