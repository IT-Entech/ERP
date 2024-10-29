<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../connectDB/connectDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(print_r(sqlsrv_errors(), true));
}
$currentYear = date("Y");
$year_no = isset($_GET['year_no']) ? $_GET['year_no'] : $currentYear;
$segment = isset($_GET['segment']) ? $_GET['segment'] : NULL;
$Sales = isset($_GET['Sales']) ? $_GET['Sales'] : NULL;
$is_new = isset($_GET['is_new']) ? $_GET['is_new'] : NULL;
$channel = isset($_GET['channel']) ? $_GET['channel'] : NULL;

$combinedKey = "{$year_no}_{$is_new}";

switch ($combinedKey) {
    case '2023':
        $target = '200000000';
        break;
    case '2024_0':
        $target = '100000000';
        break;
    case '2024_Y':
        $target = '58000000';
        break;
    case '2024_N':
        $target = '42000000';
        break;
    default:
        $target = '100000000'; // Default value if no match found
}


if($segment == '999' && $Sales == 'N' && $is_new == 0){
$sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target,
    COUNT(A.so_no) AS so_no
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($year_no );
}elseif($segment <> '999' && $Sales == 'N' && $is_new == 0){
    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no  AND customer_segment_code = ?
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target,
    COUNT(A.so_no) AS so_no
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($segment, $year_no);
}elseif($segment == '999' && $Sales == 'N' && $is_new <> 0){
    switch ($is_new) {
        case 'Y':
            $is_new_array = ['01', '02', '04'];
            break;
        case 'N':
            $is_new_array = ['03'];
            break;
        default:
            $is_new_array = [0]; // Default case
            break;
    }
    $is_new_list = "'" . implode("','", $is_new_array) . "'";
    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no AND A2.status IN ($is_new_list)
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target,
    COUNT(A.so_no) AS so_no
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($year_no);
}elseif($segment == '999' && $Sales <> 'N' && $is_new == 0){
    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no AND A2.staff_id = ?
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target,
    COUNT(A.so_no) AS so_no
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($Sales, $year_no);
}else{
    switch ($is_new) {
        case 'Y':
            $is_new_array = ['01', '02', '04'];
            break;
        case 'N':
            $is_new_array = ['03'];
            break;
        default:
            $is_new_array = [0]; // Default case
            break;
    }
    $is_new_list = "'" . implode("','", $is_new_array) . "'";
    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no AND customer_segment_code = ? AND staff_id = ? AND A2.status IN ($is_new_list)
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target,
    COUNT(A.so_no) AS so_no
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($segment, $Sales, $year_no);
}

$stmt = sqlsrv_query($objCon, $sqlrevenue_accu, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
$graphData = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $graphData[] = $row;   
}

$data = [
    'graphData' => $graphData
];
sqlsrv_close($objCon);
header('Content-Type: application/json');
echo json_encode($data);
?>