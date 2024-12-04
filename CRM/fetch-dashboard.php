<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../connectDB/connectWebDB.php'; // Include your database connection script
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(print_r(sqlsrv_errors(), true));
}
$level = isset($_GET['Level']) ? $_GET['Level'] : NULL;
$currentYear = date("Y");
$currentMonth = date("m");
$year_no = isset($_GET['year_no']) ? $_GET['year_no'] : $currentYear;
$month_no = isset($_GET['month_no']) ? $_GET['month_no'] : $currentMonth;
$channel = isset($_GET['channel']) ? $_GET['channel'] : NULL;
$Sales = isset($_GET['Sales']) ? $_GET['Sales'] : NULL;
$is_new = isset($_GET['is_new']) ? $_GET['is_new'] : NULL;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $period = $_POST['period'];
    echo $period;

}
$is_new_array = match ($is_new) {
  'Y' => ['01', '02', '04'],
  'N' => ['03'],
  default => [] // Optional: handle other cases if needed
};

$is_new_list = "'" . implode("','", $is_new_array) . "'";
if ($year_no <> 0 && $month_no == 0 && $channel == 'N' && $Sales == 'N' && $is_new == 0) {
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";

// Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";

// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";

// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          GROUP BY 
          b.customer_segment_name";

// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";

// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          GROUP BY 
          C.customer_segment_name";

$params = array($year_no);
}else if($year_no <> 0 && $month_no != 0 && $channel == 'N' && $Sales == 'N' && $is_new == 0){
  // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND month_no = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no);
}else if($year_no <> 0 && $month_no == 0 && $channel != 'N' && $Sales == 'N' && $is_new == 0){
 // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND is_call = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND sales_channels_group_code = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.sales_channels_group_code = ?
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $channel);
}else if($year_no <> 0 && $month_no == 0 && $channel == 'N' && $Sales != 'N' && $is_new == 0){
   // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND C.staff_id = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.staff_id = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.staff_id = ?
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.staff_id = ?
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $Sales);
}else if($year_no <> 0 && $month_no == 0 && $channel == 'N' && $Sales == 'N' && $is_new != 0){
      // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND C.is_new = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $is_new);
}else if($year_no <> 0 && $month_no != 0 && $channel != 'N' && $Sales == 'N' && $is_new == 0){
     // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          AND is_call = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          AND sales_channels_group_code = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.sales_channels_group_code = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          AND a.sales_channels_group_code = ?
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.sales_channels_group_code = ?
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no, $channel);
}else if($year_no <> 0 && $month_no != 0 && $channel == 'N' && $Sales != 'N' && $is_new == 0){
       // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          AND C.staff_id = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.staff_id = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          AND a.staff_id = ?
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.staff_id = ?
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no, $Sales);
}else if($year_no <> 0 && $month_no != 0 && $channel == 'N' && $Sales == 'N' && $is_new != 0){
       // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          AND C.is_new = ? 
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no, $is_new);
}else if($year_no <> 0 && $month_no == 0 && $channel != 'N' && $Sales != 'N' && $is_new == 0){
  // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND is_call = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND sales_channels_group_code = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          AND C.staff_id = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          AND A.staff_id = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.sales_channels_group_code = ?
          AND staff_id = ?
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          AND staff_id = ?
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $channel, $Sales);
}else if($year_no <> 0 && $month_no == 0 && $channel != 'N' && $Sales == 'N' && $is_new != 0){
    // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND is_call = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND sales_channels_group_code = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          AND C.is_new = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.sales_channels_group_code = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $channel, $is_new);
}else if($year_no <> 0 && $month_no == 0 && $channel == 'N' && $Sales != 'N' && $is_new != 0){
      // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND staff_id = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND C.staff_id = ? 
          AND C.is_new = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.staff_id = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.staff_id = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.staff_id = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $Sales, $is_new);
}else if($year_no <> 0 && $month_no != 0 && $channel != 'N' && $Sales != 'N' && $is_new == 0){
     // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          AND is_call = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          AND sales_channels_group_code = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          AND staff_id = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.sales_channels_group_code = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          AND a.sales_channels_group_code = ?
          AND staff_id = ?
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.sales_channels_group_code = ?
          AND staff_id = ?
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no, $channel, $Sales);
}else if($year_no <> 0 && $month_no != 0 && $channel != 'N' && $Sales == 'N' && $is_new != 0){
       // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          AND is_call = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          AND sales_channels_group_code = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          AND C.is_new = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.sales_channels_group_code = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          AND a.sales_channels_group_code = ?
          AND a.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.sales_channels_group_code = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no, $channel, $is_new);
}else if($year_no <> 0 && $month_no != 0 && $channel == 'N' && $Sales != 'N' && $is_new != 0){
         // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          AND staff_id = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          AND staff_id = ? 
          AND C.is_new = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND staff_id = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          AND staff_id = ?
          AND a.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND staff_id = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no, $Sales, $is_new);
}else if($year_no <> 0 && $month_no == 0 && $channel != 'N' && $Sales != 'N' && $is_new != 0){
    // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND is_call = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND sales_channels_group_code = ?
          AND staff_id = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          AND staff_id = ?
          AND C.is_new = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          AND staff_id = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.sales_channels_group_code = ?
          AND staff_id = ?
          AND a.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND A.sales_channels_group_code = ?
          AND staff_id = ?
          AND status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $channel, $Sales, $is_new);
}else{
     // Appointment query
$sqlappoint = "SELECT 
          FORMAT(appoint_date, 'dd-MM') AS format_date,
          COUNT(appoint_no) AS appoint_no,
          CASE WHEN is_status <> '4' THEN COUNT(appoint_no) END AS appoint_quality
          FROM 
          appoint_head
          WHERE 
          year_no = ?
          AND month_no = ?
          AND is_call = ?
          AND staff_id = ?
          GROUP BY 
          FORMAT(appoint_date, 'dd-MM'), is_status
          ORDER BY 
          format_date ASC";
// Cost sheet query
$sqlcostsheet = "SELECT 
          FORMAT(qt_date, 'yyyy-MM') AS format_date,
          SUM(so_amount) AS so_amount,
          COUNT(qt_no) AS qt_no
          FROM 
          cost_sheet_head
          WHERE 
          is_status <> 'C' 
          AND YEAR(qt_date) = ?
          AND MONTH(qt_date) = ?
          AND sales_channels_group_code = ?
          AND staff_id = ?
          AND is_new = ?
          GROUP BY 
          FORMAT(qt_date, 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Order query
$sqlorder = "SELECT 
          MONTH(A.shipment_date) AS month_no,
          SUM(A.total_before_vat) AS order_amount,
           COUNT(A.order_no) AS order_no
          FROM 
          order_head A
          LEFT JOIN 
          so_detail B ON A.order_no = B.order_no
           LEFT JOIN 
          cost_sheet_head C ON A.qt_no = C.qt_no
          LEFT JOIN 
          plan_head D ON A.order_no = D.order_no
          WHERE 
          A.is_status <> 'C' 
          AND B.so_no IS NULL
          AND D.order_no IS NULL
          AND YEAR(A.shipment_date) = ? 
          AND MONTH(A.shipment_date) = ? 
          AND C.sales_channels_group_code = ? 
          AND staff_id = ?
          AND C.is_new = ?
          GROUP BY 
          MONTH(A.shipment_date)
          ORDER BY 
          MONTH(A.shipment_date) ASC";
   // Revenue query
$sqlrevenue = "SELECT 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
          SUM(A.total_before_vat) AS so_amount,
          COUNT(A.so_no) AS so_no
          FROM 
          View_SO_SUM A
          WHERE 
          A.year_no = ?
          AND A.month_no = ?
          AND A.sales_channels_group_code = ?
          AND staff_id = ?
          AND A.status IN ($is_new_list)
          GROUP BY 
          FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
          ORDER BY 
          format_date ASC";
// Customer segment query
$sqlsegment = "SELECT 
          b.customer_segment_name, 
          FORMAT(SUM(total_before_vat), 'N2') AS total_before_vat, 
          FORMAT(SUM(total_before_vat) / COUNT(a.customer_segment_code), 'N2') AS aov, 
          COUNT(a.customer_segment_code) AS segment_count 
          FROM 
          View_SO_SUM a
          LEFT JOIN 
          ms_customer_segment b ON a.customer_segment_code = b.customer_segment_code
          WHERE 
          a.year_no = ?
          AND a.month_no = ?
          AND a.sales_channels_group_code = ?
          AND staff_id = ?
          AND a.status IN ($is_new_list)
          GROUP BY 
          b.customer_segment_name";
// Region query
$sqlregion = "SELECT 
          C.customer_segment_name AS segment,
          COUNT(CASE WHEN B.zone_code = '01' THEN A.province_code END) AS 'North',
          COUNT(CASE WHEN B.zone_code = '02' THEN A.province_code END) AS 'Central',
          COUNT(CASE WHEN B.zone_code = '03' THEN A.province_code END) AS 'North_East',
          COUNT(CASE WHEN B.zone_code = '04' THEN A.province_code END) AS 'West',
          COUNT(CASE WHEN B.zone_code = '05' THEN A.province_code END) AS 'East',
          COUNT(CASE WHEN B.zone_code = '06' THEN A.province_code END) AS 'South'
          FROM 
          View_SO_SUM A
          LEFT JOIN 
          ms_province B ON A.province_code = B.province_code
          LEFT JOIN 
          ms_customer_segment C ON A.customer_segment_code = C.customer_segment_code
          WHERE 
          A.year_no = ?
          AND month_no = ?
          AND A.sales_channels_group_code = ?
          AND staff_id = ?
          AND status IN ($is_new_list)
          GROUP BY 
          C.customer_segment_name";
  $params = array($year_no, $month_no, $channel, $Sales, $is_new);
}

// Execute the first query
$stmt = sqlsrv_query($objCon, $sqlrevenue, $params);
if ($stmt === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

// Initialize an array to hold the first query results
$revenueData = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $revenueData[] = $row;
}
sqlsrv_free_stmt($stmt);



// Execute the second query
$stmt1 = sqlsrv_query($objCon, $sqlappoint, $params);
if ($stmt1 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute second query"]);
    exit;
}

// Initialize an array to hold the second query results
$appointData = [];
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $appointData[] = $row;
}
sqlsrv_free_stmt($stmt1);

$stmt2 = sqlsrv_query($objCon, $sqlsegment, $params);
if ($stmt2 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute segment query"]);
    exit;
}


$segmentData = [];
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $segmentData[] = $row;
}
sqlsrv_free_stmt($stmt2);


$stmt3 = sqlsrv_query($objCon, $sqlcostsheet, $params);
if ($stmt3 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute segment query"]);
    exit;
}


$costsheetData = [];
while ($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
    $costsheetData[] = $row;
}
sqlsrv_free_stmt($stmt3);

$stmt4 = sqlsrv_query($objCon, $sqlregion, $params);
if ($stmt4 === false) {
    // Log SQL errors if the query fails
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log errors for debugging purposes
    http_response_code(500); // Set HTTP status code to 500 (Internal Server Error)
    echo json_encode(["error" => "Failed to execute segment query"]); // Return error message as JSON
    exit;
}


$regionData = [];
while ($row = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)) {
    $regionData[] = $row;
}
sqlsrv_free_stmt($stmt4);

$stmt5 = sqlsrv_query($objCon, $sqlorder, $params);
if ($stmt5 === false) {
    // Log SQL errors if the query fails
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log errors for debugging purposes
    http_response_code(500); // Set HTTP status code to 500 (Internal Server Error)
    echo json_encode(["error" => "Failed to execute segment query"]); // Return error message as JSON
    exit;
}


$orderData = [];
while ($row = sqlsrv_fetch_array($stmt5, SQLSRV_FETCH_ASSOC)) {
    $orderData[] = $row;
}
sqlsrv_free_stmt($stmt5);

// Close the database connection
sqlsrv_close($objCon);

$data = [
    'revenueData' => $revenueData,
    'appointData' => $appointData,
    'segmentData' => $segmentData,
    'costsheetData' => $costsheetData,
    'regionData' => $regionData,
    'orderData' => $orderData
];

header('Content-Type: application/json');
echo json_encode($data);

