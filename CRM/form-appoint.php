<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../connectDB/connectWebDB.php';
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sql = "SELECT A.staff_id, B.fname_e, B.nick_name 
            FROM xuser AS A
            LEFT JOIN hr_staff B ON A.staff_id = B.staff_id
            WHERE gid = '16387' 
              AND usrid NOT IN ('16387', 23, '36', '42', '47', '50', '79', '80', '96', '97', '101', 
                                '104', '105', '107', '110', '112', '115', '122', 124, 125, 126, 
                                127, 128, 129, 131, 132, 133, 135, 140, 149,150) 
              AND isactive = 'Y' 
              AND A.staff_id <> ''";
$stmt = sqlsrv_query($objCon, $sql);
$sales_data = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $sales_data[] = $row;
}

$sqlchannel = "SELECT sales_channels_group_code, sales_channels_group_name
            FROM ms_sales_channels_group
            WHERE is_active = 'Y' ";
$stmt1 = sqlsrv_query($objCon, $sqlchannel);
$channel = array();
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $channel[] = $row;
}

$sqlsearch = "SELECT sales_channels_search_code, sales_channels_search_name
            FROM ms_sales_channels_search
            WHERE is_active = 'Y' ";
$stmt2 = sqlsrv_query($objCon, $sqlsearch);
$search = array();
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $search[] = $row;
}

$sqlcontact = "SELECT sales_channels_code, sales_channels_name
            FROM ms_sales_channels
            WHERE is_active = 'Y' ";
$stmt3 = sqlsrv_query($objCon, $sqlcontact);
$contact = array();
while ($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
    $contact[] = $row;
}

$sqlnation = "SELECT nationality_code, nationality_name
            FROM ms_nationality
            WHERE is_active = 'Y' ";
$stmt4 = sqlsrv_query($objCon, $sqlnation);
$nationality = array();
while ($row = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)) {
    $nationality[] = $row;
}

$sqlprovince = "SELECT province_code, province_name
            FROM ms_province
            WHERE is_active = 'Y' ";
$stmt5 = sqlsrv_query($objCon, $sqlprovince);
$province = array();
while ($row = sqlsrv_fetch_array($stmt5, SQLSRV_FETCH_ASSOC)) {
    $province[] = $row;
}

$sqlsegment = "SELECT customer_segment_code, customer_segment_name
            FROM ms_customer_segment
            WHERE is_active = 'Y' ";
$stmt6 = sqlsrv_query($objCon, $sqlsegment);
$segment = array();
while ($row = sqlsrv_fetch_array($stmt6, SQLSRV_FETCH_ASSOC)) {
    $segment[] = $row;
}

$sqlsegment = "SELECT cleaning_type_code, cleaning_type_name
            FROM ms_cleaning_type
            WHERE is_active = 'Y' ";
$stmt7 = sqlsrv_query($objCon, $sqlsegment);
$CL = array();
while ($row = sqlsrv_fetch_array($stmt7, SQLSRV_FETCH_ASSOC)) {
    $CL[] = $row;
}

$sqlis_appoint= "SELECT is_appoint_code, is_appoint_name
            FROM ms_appoint
            WHERE is_active = 'Y' ";
$stmt8 = sqlsrv_query($objCon, $sqlis_appoint);
$is_appoint = array();
while ($row = sqlsrv_fetch_array($stmt8, SQLSRV_FETCH_ASSOC)) {
    $is_appoint[] = $row;
}

$sqlis_prospect= "SELECT prospect_code, prospect_name
            FROM ms_prospect
            WHERE is_active = 'Y' ";
$stmt9 = sqlsrv_query($objCon, $sqlis_prospect);
$is_prospect = array();
while ($row = sqlsrv_fetch_array($stmt9, SQLSRV_FETCH_ASSOC)) {
    $is_prospect[] = $row;
}

$sqlappoint_status= "SELECT status_code, status_name
            FROM ms_appoint_status
            WHERE is_active = 'Y' ";
$stmt10 = sqlsrv_query($objCon, $sqlappoint_status);
$appoint_status = array();
while ($row = sqlsrv_fetch_array($stmt10, SQLSRV_FETCH_ASSOC)) {
    $appoint_status[] = $row;
}

$data = [
    'sales_data' => $sales_data,
    'channel' => $channel,
    'search' => $search,
    'contact' => $contact,
    'nationality' => $nationality,
    'province' => $province,
    'segment' => $segment,
    'CL' => $CL,
    'is_appoint' => $is_appoint,
    'is_prospect' => $is_prospect,
    'appoint_status' => $appoint_status
];

sqlsrv_close($objCon);
header('Content-Type: application/json');
echo json_encode($data);
?>