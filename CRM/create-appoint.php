<?php
include_once('C:\xampp\htdocs\connectDB\connectDB.php');
$objCon = connectDB();

$staff = isset($_POST['staff']) ? $_POST['staff'] : NULL;
$uid = "SELECT * FROM xuser WHERE staff_id LIKE '%$staff%'";
$uid = sqlsrv_query($objCon, $uid);
$uid = sqlsrv_fetch_array($uid, SQLSRV_FETCH_ASSOC);
$uids = $uid['usrid'];
print_r($_POST);
$timezone = new DateTimeZone('Asia/Bangkok'); // You can use 'Asia/Bangkok', 'Asia/Jakarta', etc.

// Create a DateTime object with the specified time zone
$date = new DateTime('now', $timezone);
$record_datetime = $date->format('Y-m-d H:i:s'); // For date and time in YYYY-MM-DD HH:MM:SS format

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$channels = isset($_POST['inputChannel']) && $_POST['inputChannel'] !== '' ? $_POST['inputChannel'] : NULL;
$inputSocial = isset($_POST['inputSocial']) && $_POST['inputSocial'] !== '' ? $_POST['inputSocial'] : NULL;
$inputContract = isset($_POST['inputContract']) && $_POST['inputContract'] !== '' ? $_POST['inputContract'] : NULL;
$inputSearch = isset($_POST['inputSearch']) && $_POST['inputSearch'] !== '' ? $_POST['inputSearch'] : NULL;
$inputAP_No = isset($_POST['inputAP_No']) && $_POST['inputAP_No'] !== '' ? $_POST['inputAP_No'] : NULL;
$inputFac_no = isset($_POST['inputFac_no']) && $_POST['inputFac_no'] !== '' ? $_POST['inputFac_no'] : NULL;
$inputFac_name = isset($_POST['inputFac_name']) && $_POST['inputFac_name'] !== '' ? $_POST['inputFac_name'] : NULL;
$inputFac_nation = isset($_POST['inputFac_nation']) && $_POST['inputFac_nation'] !== '' ? $_POST['inputFac_nation'] : NULL;
$inputFac_type = isset($_POST['inputFac_type']) && $_POST['inputFac_type'] !== '' ? $_POST['inputFac_type'] : NULL;
$inputFac_value = isset($_POST['inputFac_value']) && $_POST['inputFac_value'] !== '' ? $_POST['inputFac_value'] : NULL;
$inputDate = isset($_POST['inputDate']) && $_POST['inputDate'] !== '' ? $_POST['inputDate'] : NULL;
$inputAddress = isset($_POST['inputAddress']) && $_POST['inputAddress'] !== '' ? $_POST['inputAddress'] : NULL;
$province_code = isset($_POST['inputProvince']) && $_POST['inputProvince'] !== '' ? $_POST['inputProvince'] : NULL;


if ($province_code <> '00') {
    $sqlprovince = "SELECT * FROM ms_province WHERE province_code = '$province_code'";
    $stmt = sqlsrv_query($objCon, $sqlprovince);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $province = $row['province_name'];
} else {
    $province = NULL; // or handle the error
}

$inputCustomer = isset($_POST['inputCustomer']) && $_POST['inputCustomer'] !== '' ? $_POST['inputCustomer'] : NULL;
$inputPosition = isset($_POST['inputPosition']) && $_POST['inputPosition'] !== '' ? $_POST['inputPosition'] : NULL;
$inputEmail = isset($_POST['inputEmail']) && $_POST['inputEmail'] !== '' ? $_POST['inputEmail'] : NULL;
$inputTel = isset($_POST['inputTel']) && $_POST['inputTel'] !== '' ? $_POST['inputTel'] : NULL;
$inputSegment = isset($_POST['inputSegment']) && $_POST['inputSegment'] !== '' ? $_POST['inputSegment'] : NULL;
$inputCL_type = isset($_POST['inputCL_type']) && $_POST['inputCL_type'] !== '' ? $_POST['inputCL_type'] : '00'; // With default '00'
$inputSales = isset($_POST['inputSales']) && $_POST['inputSales'] !== '' ? $_POST['inputSales'] : NULL;
$inputAppoint = isset($_POST['inputAppoint']) && $_POST['inputAppoint'] !== '' ? $_POST['inputAppoint'] : NULL;
$inputVisit = isset($_POST['inputVisit']) && $_POST['inputVisit'] !== '' ? $_POST['inputVisit'] : NULL;

$inputCus_status = isset($_POST['inputCus_status']) && $_POST['inputCus_status'] !== '' ? $_POST['inputCus_status'] : NULL;
$inputInsight = isset($_POST['inputInsight']) && $_POST['inputInsight'] !== '' ? $_POST['inputInsight'] : NULL;
$inputCompetitor_name = isset($_POST['inputCompetitor_name']) && $_POST['inputCompetitor_name'] !== '' ? $_POST['inputCompetitor_name'] : NULL;
$inputCompetitor_value = isset($_POST['inputCompetitor_value']) && $_POST['inputCompetitor_value'] !== '' ? $_POST['inputCompetitor_value'] : NULL;
$inputis_status = isset($_POST['inputis_status']) && $_POST['inputis_status'] !== '' ? $_POST['inputis_status'] : NULL;
$inputReason = isset($_POST['inputReason']) && $_POST['inputReason'] !== '' ? $_POST['inputReason'] : NULL;
$inputRemark = isset($_POST['inputRemark']) && $_POST['inputRemark'] !== '' ? $_POST['inputRemark'] : NULL;

}
$call = 'CO';
$code = 'AP';
$time =  time();
date_default_timezone_set("Asia/Bangkok");
$timestamp = date('Y-m-d H:i:s', $time);



$Y = date("Y");
$year = substr(date("Y")+543, -2);
$month = date("m");
$month1 = date('m', strtotime('+1 month'));
$yearMonth = $year.$month;
$fpfix = $code.$yearMonth;
$maxId = "SELECT CAST (runno AS int)AS runno FROM xrunno ";
$total_record1= sqlsrv_query($objCon, $maxId, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET));
$total_record2 = sqlsrv_num_rows($total_record1);
$Mo = "SELECT * FROM xrunno WHERE doc_type IN ('AP') ORDER BY pfix desc";
$query = sqlsrv_query($objCon,$Mo);
$row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
$pfix = $row['pfix'];
$runno = $row['runno'];
$maxId = (($runno)+1); 
$lastId = substr("0000".$maxId, -4);
$call_no = $call.$yearMonth.$lastId;
$appoint_no = $code.$yearMonth.$lastId;
$appoint_no1 = $code.'-'.$yearMonth.'-'.$maxId;
$i = 0;
$newIds = $i++;
$newId = substr("0000".$newIds+1, -4);
$call_new = $call.$yearMonth.$newId;
$appoint_new = $code.$yearMonth.$newId;


if($fpfix === $pfix){
    $update = "UPDATE xrunno SET runno = ? WHERE pfix = ?"; 
    $params = array($maxId, $pfix);
    $objQuery = sqlsrv_query($objCon, $update, $params); 
   
}else{
   
    $insert = "INSERT INTO xrunno(doc_type, pfix, runno) VALUES(?, ?, ?)"; 
    $params = array( $code, $fpfix, $newId);
    $objQuery = sqlsrv_query($objCon, $insert, $params); 
}
if ($objQuery === false) {
    die(print_r(sqlsrv_errors(), true));
} 
    
$is_app = 'W';

if($fpfix === $pfix){
    $strSQL = "INSERT INTO [dbo].[appoint_head]
           ([record_id]
           ,[record_date]
           ,[update_id]
           ,[update_date]
           ,[appoint_no]
           ,[appoint_date]
           ,[visit_date]
           ,[call_no]
           ,[call_date]
           ,[c_factory_no]
           ,[customer_name]
           ,[province_name]
           ,[contact_name]
           ,[position_code]
           ,[contact_tel]
           ,[contact_mail]
           ,[is_status]
           ,[is_qt]
           ,[cost_sheet_date]
           ,[qt_no]
           ,[qt_date]
           ,[is_cust_confirm]
           ,[issue_no]
           ,[issue_date]
           ,[analyze_date]
           ,[request_no]
           ,[request_date]
           ,[shipment_date]
           ,[remark]
           ,[staff_id]
           ,[supplier_code]
           ,[supplier_account_no]
           ,[is_request_qt_dispose]
           ,[is_call]
           ,[appoint_time]
           ,[total_year_amount]
           ,[month_no]
           ,[year_no]
           ,[close_sale_date]
           ,[not_sale_reasoning_code]
           ,[not_sale_date]
           ,[customer_code]
           ,[customer_account_no]
           ,[address]
           ,[is_prospect]
           ,[is_appoint]
           ,[appoint_name]
           ,[appoint_position_code]
           ,[appoint_tel]
           ,[next_appoint_date]
           ,[sales_channels_code]
           ,[sales_type_code]
           ,[is_routine]
           ,[why_contact]
           ,[search_content]
           ,[total_amount]
           ,[is_supplier_price]
           ,[revision_no]
           ,[province_code]
           ,[customer_segment_code]
           ,[competitor_price]
           ,[work_date_plan]
           ,[is_app]
           ,[sales_channels_search_code]
           ,[industry_name]
           ,[registered_capital]
           ,[cust_insight]
           ,[competitor_name]
           ,[reasoning]
           ,[nationality_code]
           ,[cleaning_type_code]) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?   
           , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
           , ?)"; 
    $params = array( $uids, $record_datetime, $uids, $record_datetime, $appoint_no, $inputDate, $inputVisit, $call_no, $inputDate, $inputFac_no,
     $inputFac_name, $province, $inputCustomer, $inputPosition, $inputTel, $inputEmail, $inputis_status, '00', NULL, NULL, 
    NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, $inputRemark, $inputSales,
    NULL, NULL, 'N', $channels, NULL, '0.00', $month, $Y, NULL, '00',
    NULL, NULL, NULL, $inputAddress, '00', $inputAppoint, NULL, NULL, NULL, NULL,
    $inputContract, '00', '0', NULL, $inputSearch, NULL, NULL, '1', $province_code, $inputSegment,
    $inputCompetitor_value, NULL, $is_app, $inputSocial, $inputFac_type, $inputFac_value, $inputInsight, $inputCompetitor_name, $inputReason, $inputFac_nation,
    $inputCL_type);
    $objQuery = sqlsrv_query($objCon, $strSQL, $params);

    $strSQL1 = "INSERT INTO appoint_follow ([record_id]
           ,[record_date]
           ,[update_id]
           ,[update_date]
           ,[appoint_no]
           ,[seq]
           ,[follow_date]
           ,[follow_detail]
           ,[is_prospect]) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $params = array( $uids, $record_datetime, $uids, $record_datetime, $appoint_no, 1, $record_datetime, NULL, $inputCus_status);
    $objQuery1 = sqlsrv_query($objCon, $strSQL1, $params);
}else{
    $strSQL = "INSERT INTO [dbo].[appoint_head]
    ([record_id]
    ,[record_date]
    ,[update_id]
    ,[update_date]
    ,[appoint_no]
    ,[appoint_date]
    ,[visit_date]
    ,[call_no]
    ,[call_date]
    ,[c_factory_no]
    ,[customer_name]
    ,[province_name]
    ,[contact_name]
    ,[position_code]
    ,[contact_tel]
    ,[contact_mail]
    ,[is_status]
    ,[is_qt]
    ,[cost_sheet_date]
    ,[qt_no]
    ,[qt_date]
    ,[is_cust_confirm]
    ,[issue_no]
    ,[issue_date]
    ,[analyze_date]
    ,[request_no]
    ,[request_date]
    ,[shipment_date]
    ,[remark]
    ,[staff_id]
    ,[supplier_code]
    ,[supplier_account_no]
    ,[is_request_qt_dispose]
    ,[is_call]
    ,[appoint_time]
    ,[total_year_amount]
    ,[month_no]
    ,[year_no]
    ,[close_sale_date]
    ,[not_sale_reasoning_code]
    ,[not_sale_date]
    ,[customer_code]
    ,[customer_account_no]
    ,[address]
    ,[is_prospect]
    ,[is_appoint]
    ,[appoint_name]
    ,[appoint_position_code]
    ,[appoint_tel]
    ,[next_appoint_date]
    ,[sales_channels_code]
    ,[sales_type_code]
    ,[is_routine]
    ,[why_contact]
    ,[search_content]
    ,[total_amount]
    ,[is_supplier_price]
    ,[revision_no]
    ,[province_code]
    ,[customer_segment_code]
    ,[competitor_price]
    ,[work_date_plan]
    ,[is_app]
    ,[sales_channels_search_code]
    ,[industry_name]
    ,[registered_capital]
    ,[cust_insight]
    ,[competitor_name]
    ,[reasoning]
    ,[nationality_code]
    ,[cleaning_type_code]) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?   
    , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    , ?)"; 
$params = array( $uids, $record_datetime, $uids, $record_datetime, $appoint_new, $inputDate, $inputVisit, $call_new, $inputDate, $inputFac_no, $inputFac_name, $province
, $inputCustomer, $inputPosition, $inputTel, $inputEmail, '0', '00', NULL, NULL, NULL, '1'
, NULL, NULL, NULL, NULL, NULL, NULL, $inputRemark, $inputSales, NULL, NULL
, 'N', $channels, NULL, '0.00', $month, $Y, NULL, '00',NULL, NULL
, NULL, $inputAddress, '00', $inputAppoint, NULL, NULL, NULL, NULL, $inputContract, '00'
, '0', NULL, $inputSearch, NULL, NULL, '1', $province_code, $inputSegment, $inputCompetitor_value, NULL
, $is_app, $inputSocial, $inputFac_name, $inputFac_value, $inputInsight, $inputCompetitor_name, $inputReason, $inputFac_nation, NULL, NULL
,$inputCL_type);
$objQuery = sqlsrv_query($objCon, $strSQL, $params);

    $strSQL1 = "INSERT INTO appoint_follow ([record_id]
    ,[record_date]
    ,[update_id]
    ,[update_date]
    ,[appoint_no]
    ,[seq]
    ,[follow_date]
    ,[follow_detail]
    ,[is_prospect]) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
$params = array( $uids, $record_datetime, $uids, $record_datetime, $appoint_new, 1, $record_datetime, NULL, $inputCus_status);
$objQuery1 = sqlsrv_query($objCon, $strSQL1, $params);
}
if ($objQuery === false) {
    die(print_r(sqlsrv_errors(), true));
 } else{
    /*
    echo '<script>alert("เพิ่มข้อมูลแล้ว");window.location="forms-appoint.php";</script>';
 */
    }



if ($objQuery1 === false) {
   die(print_r(sqlsrv_errors(), true));
} 

sqlsrv_close($objCon);
?>