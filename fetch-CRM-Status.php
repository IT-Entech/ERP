<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('C:\xampp\htdocs\connectDB\connectDB.php');
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
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
                   FROM cost_sheet_head A
                   LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
				   LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
                   LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
                   WHERE A.is_prospect <> '00' AND MONTH(A.qt_date) = ? AND YEAR(A.qt_date) = ? AND is_status <> 'C'  AND B.so_no IS NULL
                   ORDER BY qt_date DESC";
     $sqlrevenue = "SELECT 
                    FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
                    SUM(A.total_before_vat) AS so_amount,
                    COUNT(A.so_no) AS so_no
                    FROM 
                    View_SO_SUM A
                    WHERE 
                    A.month_no = ?
                    AND A.year_no = ?
                    GROUP BY 
                    FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM')
                    ORDER BY 
                    format_date ASC";
     $sqlap = "SELECT 
                    FORMAT(appoint_date, 'yyyy-MM') AS format_date,
                    COUNT(CASE WHEN qt_no IS NULL AND is_status <> 4 THEN appoint_no END) AS appoint_no,
                    COUNT(CASE WHEN qt_no IS NULL AND is_status = 4 THEN appoint_no END) AS specific_appoint_no
                    FROM 
                    appoint_head
                    WHERE 
                    month_no = ? AND year_no = ? AND qt_no IS NULL
                    GROUP BY 
                    FORMAT(appoint_date, 'yyyy-MM')
                    ORDER BY 
                    format_date ASC";
     $sqlcostsheet = "SELECT 
                  FORMAT(qt_date, 'yyyy-MM') AS format_date,
	                SUM(so_amount)AS so_amount,
                  COUNT(A.qt_no) AS qt_no,
				  COUNT(CASE WHEN  is_prospect IS NULL    THEN A.qt_no END) AS Unknownss,
				   COUNT(CASE WHEN  print_qt_count = 0   THEN A.qt_no END) AS Unknowns,
				 COUNT(CASE WHEN  is_prospect = '00' AND print_qt_count = 0 THEN A.qt_no END) AS Unknown,
				  SUM(CASE WHEN  is_prospect = '00' AND is_tracking IN ('1','3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
				  SUM(CASE WHEN  is_prospect = '00' AND is_tracking IN ('2','4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
				  COUNT(CASE WHEN  is_prospect = '05' THEN A.qt_no END) AS potential,
				  SUM(CASE WHEN  is_prospect = '05' AND is_tracking IN ('1','3') THEN so_amount END) AS potential_amount,
				  SUM(CASE WHEN  is_prospect = '05' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_potential_amount,
				  COUNT(CASE WHEN  is_prospect = '04' THEN A.qt_no END) AS prospect,
				  SUM(CASE WHEN  is_prospect = '04'AND is_tracking IN ('1','3') THEN so_amount END) AS prospect_amount,
				   SUM(CASE WHEN  is_prospect = '04' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_prospect_amount,
				  COUNT(CASE WHEN  is_prospect = '06' THEN A.qt_no END) AS pipeline,
				  SUM(CASE WHEN  is_prospect = '06'AND is_tracking IN ('1','3') THEN so_amount END) AS pipeline_amount,
				  SUM(CASE WHEN  is_prospect = '06' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_pipeline_amount
                  FROM 
                  cost_sheet_head A
                  WHERE 
                  is_status <> 'C'  AND MONTH(qt_date) = ?  AND YEAR(qt_date) = ? 
				  AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
                  GROUP BY 
                  FORMAT(qt_date, 'yyyy-MM')
                  ORDER BY 
                  format_date ASC";
                   $params = array($month_no, $year_no);
}else{
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
                   FROM cost_sheet_head A
                   LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
				   LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
                   LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
                   WHERE A.is_prospect <> '00' AND MONTH(A.qt_date) = ? AND YEAR(A.qt_date) = ? AND A.staff_id = ?  AND is_status <> 'C'  AND B.so_no IS NULL
                   ORDER BY qt_date DESC";
     $sqlap = "SELECT 
     FORMAT(appoint_date, 'yyyy-MM') AS format_date,
     COUNT(CASE WHEN qt_no IS NULL AND is_status <> 4 THEN appoint_no END) AS appoint_no,
     COUNT(CASE WHEN qt_no IS NULL AND is_status = 4 THEN appoint_no END) AS specific_appoint_no
     FROM 
     appoint_head
     WHERE 
     month_no = ? AND year_no = ? AND staff_id = ? AND qt_no IS NULL
     GROUP BY 
     FORMAT(appoint_date, 'yyyy-MM')
     ORDER BY 
     format_date ASC";
         $sqlcostsheet = "SELECT 
         FORMAT(qt_date, 'yyyy-MM') AS format_date,
	                SUM(so_amount)AS so_amount,
                  COUNT(A.qt_no) AS qt_no,
				  COUNT(CASE WHEN  is_prospect IS NULL    THEN A.qt_no END) AS Unknownss,
				   COUNT(CASE WHEN  print_qt_count = 0   THEN A.qt_no END) AS Unknowns,
				 COUNT(CASE WHEN  is_prospect = '00' AND print_qt_count = 0 THEN A.qt_no END) AS Unknown,
				  SUM(CASE WHEN  is_prospect = '00' AND is_tracking IN ('1','3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
				  SUM(CASE WHEN  is_prospect = '00' AND is_tracking IN ('2','4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
				  COUNT(CASE WHEN  is_prospect = '05' THEN A.qt_no END) AS potential,
				  SUM(CASE WHEN  is_prospect = '05' AND is_tracking IN ('1','3') THEN so_amount END) AS potential_amount,
				  SUM(CASE WHEN  is_prospect = '05' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_potential_amount,
				  COUNT(CASE WHEN  is_prospect = '04' THEN A.qt_no END) AS prospect,
				  SUM(CASE WHEN  is_prospect = '04'AND is_tracking IN ('1','3') THEN so_amount END) AS prospect_amount,
				   SUM(CASE WHEN  is_prospect = '04' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_prospect_amount,
				  COUNT(CASE WHEN  is_prospect = '06' THEN A.qt_no END) AS pipeline,
				  SUM(CASE WHEN  is_prospect = '06'AND is_tracking IN ('1','3') THEN so_amount END) AS pipeline_amount,
				  SUM(CASE WHEN  is_prospect = '06' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_pipeline_amount
         FROM 
         cost_sheet_head A
         WHERE 
         is_status <> 'C'  AND MONTH(qt_date) = ?  AND YEAR(qt_date) = ? AND staff_id = ? 
         AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
         GROUP BY 
         FORMAT(qt_date, 'yyyy-MM')
         ORDER BY 
         format_date ASC";
                    $sqlrevenue = "SELECT 
                    FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
                    SUM(A.total_before_vat) AS so_amount,
                    COUNT(A.so_no) AS so_no
                    FROM 
                    View_SO_SUM A
                    WHERE 
                    A.month_no = ?
                    AND A.year_no = ?
                    AND A.staff_id = ?
                    GROUP BY 
                    FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM')
                    ORDER BY 
                    format_date ASC";
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

$stmtap = sqlsrv_query($objCon, $sqlap, $params);
if ($stmtap === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

// Initialize an array to hold the first query results
$apData = [];
while ($row = sqlsrv_fetch_array($stmtap, SQLSRV_FETCH_ASSOC)) {
    $apData[] = $row;
}

$stmtqt = sqlsrv_query($objCon, $sqlcostsheet, $params);
if ($stmtqt === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

// Initialize an array to hold the first query results
$qtData = [];
while ($row = sqlsrv_fetch_array($stmtqt, SQLSRV_FETCH_ASSOC)) {
    $qtData[] = $row;
}

$stmt1 = sqlsrv_query($objCon, $sqlrevenue, $params);
if ($stmt1 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

// Initialize an array to hold the first query results
$revenueData = [];
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $revenueData[] = $row;
}
$data = [
    'revenueData' => $revenueData,
    'apData' => $apData,
    'qtData' => $qtData,
    'tableData' => $tableData
];

sqlsrv_free_stmt($stmt);
sqlsrv_free_stmt($stmtap);
sqlsrv_free_stmt($stmtqt);
sqlsrv_free_stmt($stmt1);
sqlsrv_close($objCon);

header('Content-Type: application/json');
echo json_encode($data);
