<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../connectDB/connectWebDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(print_r(sqlsrv_errors(), true));
}
$currentYear = date("Y");
$currentMonth = date("m");
$year_no = isset($_GET['year_no']) ? $_GET['year_no'] : $currentYear;
$month_no = isset($_GET['month_no']) ? $_GET['month_no'] : $currentMonth;
$is_new = isset($_GET['is_new']) ? $_GET['is_new'] : NULL;
$channel = isset($_GET['channel']) ? $_GET['channel'] : NULL;
$Sales = isset($_GET['Sales']) ? $_GET['Sales'] : NULL;


$combinedKey = "{$year_no}_{$Sales}_{$is_new}_{$channel}";

switch ($combinedKey) {
    case '2023_N_0_N':
        $target = '200000000';
        break;
    case '2024_N_0_N':
        $target = '100000000';
        break;
    case '2025_N_0_N':
        $target = '80000000';
        break;    
    case '2024_Y_N':
        $target = '58000000';
        break;
    case '2024_Y_I':
        $target = '18000000';
        break;   
    case '2024_Y_O':
        $target = '40000000';
        break;  
    case '2024_N_N':
        $target = '42000000';
        break;
    case '2024_N_O':
        $target = '22000000';
        break;
    case '2024_N_I':
        $target = '20000000';
        break;
    case '2024_N_0_O':
        $target = '62000000';
        break;
    default:
        $target = '100000000'; // Default value if no match found
}
if ($is_new === 'Y') {
    $is_new_array = ['01', '02', '04'];
} elseif ($is_new === 'N') {
    $is_new_array = ['03'];
} else {
    $is_new_array = [0]; // Default case
}

$is_new_list = "'" . implode("','", $is_new_array) . "'";

if($month_no != 'N'){
$sqlrevenue_accu = "SELECT
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat)
        FROM View_SO_SUM A2
        WHERE A2.year_no = A.year_no
        AND A2.month_no <= A.month_no
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target
FROM
    View_SO_SUM A
WHERE
    A.year_no = ?
GROUP BY
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY
    A.month_no ASC";
$params = array($year_no);
}
/*else if($is_new == 0 && $channel == 'N'){
    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no  AND customer_segment_code = ?
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($year_no);
}elseif($is_new <> 0 && $channel == 'N'){

    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.status IN ($is_new_list)
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($year_no);
}elseif($is_new <> 0 && $channel != 'N'){

    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.status IN ($is_new_list)
        AND A2.sales_channels_group_code = ?
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($channel, $year_no);
}else{
    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND customer_segment_code = ? 
        AND staff_id = ? 
        AND A2.status IN ($is_new_list)
    ) AS accumulated_so,
	A.month_no * ($target / 12) AS accumulated_target
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$params = array($year_no);
}*/

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
