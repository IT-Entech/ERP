<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../connectDB/connectDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

$currentYear = date("Y");
$currentMonth = date("m");
$Sales = isset($_GET['Sales']) ? $_GET['Sales'] : NULL;
$year_no = isset($_GET['year_no']) ? $_GET['year_no'] : $currentYear;
$month_no = isset($_GET['month_no']) ? $_GET['month_no'] : $currentMonth;
/*$channel = isset($_GET['channel']) ? $_GET['channel'] : NULL;*/

if($year_no <> 0 && $month_no <> 0 && $Sales == 'N'){
<<<<<<< Updated upstream
    $sqlappoint = "SELECT 
=======
    $sqlappoint = " SELECT 
>>>>>>> Stashed changes
                    FORMAT(A.appoint_date, 'yyyy-MM-dd') AS format_date,
					A.customer_name,
					CASE WHEN A.qt_no IS NULL AND A.is_status <> 4 THEN A.appoint_no END AS appoint_no
                    FROM 
                    appoint_head A
                    LEFT JOIN 
                    cost_sheet_head B ON A.appoint_no = B.appoint_no
                    WHERE 
                     B.appoint_no IS NULL 
                     AND A.is_status <> 4 AND month_no = ? AND year_no = ?
                    ORDER BY 
                    format_date DESC, appoint_no DESC";
                   $params = array($month_no, $year_no);
}else{
<<<<<<< Updated upstream
    $sqlappoint = "SELECT 
=======
    $sqlappoint = " SELECT 
>>>>>>> Stashed changes
                    FORMAT(A.appoint_date, 'yyyy-MM-dd') AS format_date,
					A.customer_name,
					CASE WHEN A.qt_no IS NULL AND A.is_status <> 4 THEN A.appoint_no END AS appoint_no
                    FROM 
                    appoint_head A
                    LEFT JOIN 
                    cost_sheet_head B ON A.appoint_no = B.appoint_no
                    WHERE 
                     B.appoint_no IS NULL 
                     AND A.is_status <> 4 AND month_no = ? AND year_no = ? AND A.staff_id = ?
                    ORDER BY 
                    format_date DESC, appoint_no DESC";

                   $params = array($month_no, $year_no, $Sales);
}

$stmt = sqlsrv_query($objCon, $sqlappoint, $params);

if ($stmt === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

$tableData = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $tableData[] = $row;
}

$data = [
    'tableData' => $tableData
];

sqlsrv_free_stmt($stmt);
sqlsrv_close($objCon);

header('Content-Type: application/json');
echo json_encode($data);
