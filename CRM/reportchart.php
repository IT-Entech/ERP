<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../connectDB/connectDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(print_r(sqlsrv_errors(), true));
}
$currentYear = date("Y");
$currentMonth = date("m");
$year_no = isset($_GET['year_no']) ? $_GET['year_no'] : $currentYear;
$month_no = isset($_GET['month_no']) ? $_GET['month_no'] : $currentMonth;
$segment = isset($_GET['segment']) ? $_GET['segment'] : NULL;
$is_new = isset($_GET['is_new']) ? $_GET['is_new'] : NULL;
$channel = isset($_GET['channel']) ? $_GET['channel'] : NULL;

$EachMonth = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
	A.month_no * (18000000 / 12) AS accumulated_target
FROM 
    View_SO_SUM A
WHERE 
    A.year_no = 2024
AND A.month_no = ?
GROUP BY 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM'), A.month_no, A.year_no
ORDER BY 
    A.month_no ASC";
$param = array($month_no);
$stmt1 = sqlsrv_query($objCon, $EachMonth, $param);
$row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC);
$targetMonth = $row['accumulated_target'];
$combinedKey = "{$year_no}_{$is_new}_{$channel}";

switch ($combinedKey) {
    case '2023':
        $target = '200000000';
        break;
    case '2024_0':
        $target = '100000000';
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

if($segment == '999' && $is_new == 0 && $channel == 'N'){
$sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
     (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.customer_segment_code ='01'
    ) AS product_so,
     (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.customer_segment_code ='02'
    ) AS product_so2,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.customer_segment_code ='03'
    ) AS product_so3,
     (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.customer_segment_code ='04'
    ) AS product_so4,
     (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.customer_segment_code ='99'
    ) AS product_so99,
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
$params = array($year_no);
}elseif($segment <> '999'  && $is_new == 0 && $channel == 'N'){
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
}elseif($segment == '999' && $is_new <> 0 && $channel == 'N'){

    $sqlrevenue_accu = "SELECT 
    FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'MMM') AS format_date,
    (
        SELECT SUM(A2.total_before_vat) 
        FROM View_SO_SUM A2 
        WHERE A2.year_no = A.year_no 
        AND A2.month_no <= A.month_no 
        AND A2.status IN ($is_new_list)
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
}elseif($segment == '999' && $is_new <> 0 && $channel != 'N'){

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