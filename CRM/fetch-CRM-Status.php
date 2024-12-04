<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../connectDB/connectDB.php';
$objCon = connectDB(); 

if ($objCon === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

$currentYear = date("Y");
$currentMonth = date("m");
$Sales = isset($_GET['Sales']) ? $_GET['Sales'] : NULL;
$year_no = isset($_GET['year_no']) ? $_GET['year_no'] : $currentYear;
$month_no = isset($_GET['month_no']) ? $_GET['month_no'] : $currentMonth;
$track = isset($_GET['tracking']) ? $_GET['tracking'] : NULL;

if($year_no <> 0 && $month_no == 'N' && $Sales == 'N' && $track == 'N'){    
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
                   FROM cost_sheet_head A
                   LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
				   LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
                   LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
                   WHERE A.is_prospect <> '00' 
                   AND is_status <> 'C'  
                   AND B.so_no IS NULL
                   AND YEAR(A.qt_date) = ?
                   ORDER BY qt_date DESC";
     $sqlrevenue = "SELECT 
                    FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
                    SUM(A.total_before_vat) AS so_amount,
                    COUNT(A.so_no) AS so_no
                    FROM 
                    View_SO_SUM A
                    WHERE 
                    A.year_no = ?
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
                    qt_no IS NULL
                    AND year_no = ?
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
                  is_status <> 'C'   
				  AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
                  AND YEAR(qt_date) = ?
                  GROUP BY 
                  FORMAT(qt_date, 'yyyy-MM')
                  ORDER BY 
                  format_date ASC";
                   $params = array($year_no);
}else if($year_no <> 0 && $month_no != 'N' && $Sales == 'N' && $track == 'N'){
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
                   FROM cost_sheet_head A
                   LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
				   LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
                   LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
                   WHERE A.is_prospect <> '00' 
                   AND is_status <> 'C'  
                   AND B.so_no IS NULL
                   AND YEAR(A.qt_date) = ?
                   AND MONTH(A.qt_date) = ?
                   ORDER BY qt_date DESC";
     $sqlrevenue = "SELECT 
                    FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
                    SUM(A.total_before_vat) AS so_amount,
                    COUNT(A.so_no) AS so_no
                    FROM 
                    View_SO_SUM A
                    WHERE 
                    A.year_no = ?
                    AND A.month_no = ?
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
                    qt_no IS NULL
                    AND year_no = ? 
                    AND month_no = ?
                    GROUP BY 
                    FORMAT(appoint_date, 'yyyy-MM')
                    ORDER BY 
                    format_date ASC";
     $sqlcostsheet = "SELECT 
                FORMAT(qt_date, 'yyyy-MM') AS format_date,
	            SUM(so_amount)AS so_amount,
                COUNT(A.qt_no) AS qt_no,
	            COUNT(CASE WHEN  is_prospect IS NULL THEN A.qt_no END) AS Unknownss,
                COUNT(CASE WHEN  print_qt_count = 0  THEN A.qt_no END) AS Unknowns,
				COUNT(CASE WHEN  is_prospect = '00' AND print_qt_count = 0 THEN A.qt_no END) AS Unknown,
                SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('1','3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
                SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('2','4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
				COUNT(CASE WHEN  is_prospect = '05' THEN A.qt_no END) AS potential,
				SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('1','3') THEN so_amount END) AS potential_amount,
				SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_potential_amount,
				COUNT(CASE WHEN  is_prospect = '04' THEN A.qt_no END) AS prospect,
				SUM  (CASE WHEN  is_prospect = '04' AND is_tracking IN ('1','3') THEN so_amount END) AS prospect_amount,
				SUM  (CASE WHEN  is_prospect = '04' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_prospect_amount,
				COUNT(CASE WHEN  is_prospect = '06' THEN A.qt_no END) AS pipeline,
				SUM  (CASE WHEN  is_prospect = '06' AND is_tracking IN ('1','3') THEN so_amount END) AS pipeline_amount,
				SUM  (CASE WHEN  is_prospect = '06' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_pipeline_amount
                FROM 
                  cost_sheet_head A
                  WHERE 
                  is_status <> 'C' 
                  AND YEAR(qt_date) = ? 
                  AND MONTH(qt_date) = ? 
				  AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
                  GROUP BY 
                  FORMAT(qt_date, 'yyyy-MM')
                  ORDER BY 
                  format_date ASC";
                   $params = array($year_no, $month_no);
}else if($year_no <> 0 && $month_no == 'N' && $track != 'N' && $Sales == 'N'){
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
                   FROM cost_sheet_head A
                   LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
				   LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
                   LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
                   WHERE A.is_prospect <> '00' 
                   AND is_status <> 'C'  
                   AND B.so_no IS NULL
                   AND YEAR(A.qt_date) = ?
                   AND is_tracking = ?
                   ORDER BY qt_date DESC";
     $sqlrevenue = "SELECT 
                    FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
                    SUM(A.total_before_vat) AS so_amount,
                    COUNT(A.so_no) AS so_no
                    FROM 
                    View_SO_SUM A
                    WHERE 
                    A.year_no = ?
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
                    qt_no IS NULL
                    AND year_no = ? 
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
				SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('1','3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
				SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('2','4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
				COUNT(CASE WHEN  is_prospect = '05' THEN A.qt_no END) AS potential,
				SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('1','3') THEN so_amount END) AS potential_amount,
				SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_potential_amount,
				COUNT(CASE WHEN  is_prospect = '04' THEN A.qt_no END) AS prospect,
				SUM  (CASE WHEN  is_prospect = '04'AND is_tracking IN ('1','3') THEN so_amount END) AS prospect_amount,
				SUM  (CASE WHEN  is_prospect = '04' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_prospect_amount,
				COUNT(CASE WHEN  is_prospect = '06' THEN A.qt_no END) AS pipeline,
				SUM  (CASE WHEN  is_prospect = '06'AND is_tracking IN ('1','3') THEN so_amount END) AS pipeline_amount,
				SUM  (CASE WHEN  is_prospect = '06' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_pipeline_amount
                  FROM 
                  cost_sheet_head A
                  WHERE 
                  is_status <> 'C' 
                  AND YEAR(qt_date) = ? 
                  AND is_tracking = ?
				  AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
                  GROUP BY 
                  FORMAT(qt_date, 'yyyy-MM')
                  ORDER BY 
                  format_date ASC";
                   $params = array($year_no, $track);
}else if($year_no <> 0 && $month_no == 'N' && $track == 'N' && $Sales != 'N'){
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,
A.appoint_no,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
    FROM cost_sheet_head A
    LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
    LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
    LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
    WHERE A.is_prospect <> '00' 
    AND is_status <> 'C'  
    AND B.so_no IS NULL
    AND YEAR(A.qt_date) = ?
    AND staff_id = ?
    ORDER BY qt_date DESC";
$sqlrevenue = "SELECT 
     FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
     SUM(A.total_before_vat) AS so_amount,
     COUNT(A.so_no) AS so_no
     FROM 
     View_SO_SUM A
     WHERE 
     A.year_no = ?
     AND staff_id = ?
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
     qt_no IS NULL
     AND year_no = ? 
     AND staff_id = ?
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
 SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('1','3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
 SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('2','4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
 COUNT(CASE WHEN  is_prospect = '05' THEN A.qt_no END) AS potential,
 SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('1','3') THEN so_amount END) AS potential_amount,
 SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_potential_amount,
 COUNT(CASE WHEN  is_prospect = '04' THEN A.qt_no END) AS prospect,
 SUM  (CASE WHEN  is_prospect = '04'AND is_tracking IN ('1','3') THEN so_amount END) AS prospect_amount,
 SUM  (CASE WHEN  is_prospect = '04' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_prospect_amount,
 COUNT(CASE WHEN  is_prospect = '06' THEN A.qt_no END) AS pipeline,
 SUM  (CASE WHEN  is_prospect = '06'AND is_tracking IN ('1','3') THEN so_amount END) AS pipeline_amount,
 SUM  (CASE WHEN  is_prospect = '06' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_pipeline_amount
   FROM 
   cost_sheet_head A
   WHERE 
   is_status <> 'C' 
   AND YEAR(qt_date) = ? 
   AND staff_id = ?
   AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
   GROUP BY 
   FORMAT(qt_date, 'yyyy-MM')
   ORDER BY 
   format_date ASC";
    $params = array($year_no, $Sales);
}else if($year_no <> 0 && $month_no != 'N' && $track != 'N' && $Sales == 'N'){
    $sqlappoint = "SELECT 
        FORMAT(A.appoint_date, 'dd-MM-yyyy') AS appoint_date,
        A.appoint_no,
        A.customer_name,
        A.qt_no,
        FORMAT(A.so_amount, 'N2') AS so_amount,
        pp.prospect_name,
        pp.prospect_code,
        A.remark,
        ms.status_name,
        ms.status_code,
        A.reasoning
    FROM cost_sheet_head A
    LEFT JOIN ms_appoint_status ms ON A.is_tracking = ms.status_code
    LEFT JOIN ms_prospect pp ON A.is_prospect = pp.prospect_code
    LEFT JOIN so_customer_status B ON A.qt_no = B.qt_no
    WHERE A.is_prospect <> '00'
      AND A.is_status <> 'C'
      AND B.so_no IS NULL
      AND YEAR(A.qt_date) = ?
      AND MONTH(A.qt_date) = ?
      AND A.is_tracking = ?
    ORDER BY A.qt_date DESC;
";
$sqlrevenue = "SELECT 
        FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
        SUM(A.total_before_vat) AS so_amount,
        COUNT(A.so_no) AS so_no
    FROM View_SO_SUM A
    WHERE A.year_no = ?
      AND A.month_no = ?
    GROUP BY FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
    ORDER BY format_date ASC;
";
$sqlap = "SELECT 
        FORMAT(appoint_date, 'yyyy-MM') AS format_date,
        COUNT(CASE WHEN qt_no IS NULL AND is_status <> 4 THEN appoint_no END) AS appoint_no,
        COUNT(CASE WHEN qt_no IS NULL AND is_status = 4 THEN appoint_no END) AS specific_appoint_no
    FROM appoint_head
    WHERE qt_no IS NULL
      AND year_no = ?
      AND month_no = ?
    GROUP BY FORMAT(appoint_date, 'yyyy-MM')
    ORDER BY format_date ASC;
";
$sqlcostsheet = "SELECT 
        FORMAT(A.qt_date, 'yyyy-MM') AS format_date,
        SUM(A.so_amount) AS so_amount,
        COUNT(A.qt_no) AS qt_no,
        COUNT(CASE WHEN A.is_prospect IS NULL THEN A.qt_no END) AS unknown_prospects,
        COUNT(CASE WHEN A.print_qt_count = 0 THEN A.qt_no END) AS no_print_count,
        COUNT(CASE WHEN A.is_prospect = '00' AND A.print_qt_count = 0 THEN A.qt_no END) AS unknown_with_no_print,
        SUM(CASE WHEN A.is_prospect = '00' AND A.is_tracking IN ('1', '3') AND A.print_qt_count = 0 THEN A.so_amount END) AS unknown_amount,
        SUM(CASE WHEN A.is_prospect = '00' AND A.is_tracking IN ('2', '4') AND A.print_qt_count = 0 THEN A.so_amount END) AS lost_unknown_amount,
        COUNT(CASE WHEN A.is_prospect = '05' THEN A.qt_no END) AS potential,
        SUM(CASE WHEN A.is_prospect = '05' AND A.is_tracking IN ('1', '3') THEN A.so_amount END) AS potential_amount,
        SUM(CASE WHEN A.is_prospect = '05' AND A.is_tracking IN ('2', '4') THEN A.so_amount END) AS lost_potential_amount,
        COUNT(CASE WHEN A.is_prospect = '04' THEN A.qt_no END) AS prospect,
        SUM(CASE WHEN A.is_prospect = '04' AND A.is_tracking IN ('1', '3') THEN A.so_amount END) AS prospect_amount,
        SUM(CASE WHEN A.is_prospect = '04' AND A.is_tracking IN ('2', '4') THEN A.so_amount END) AS lost_prospect_amount,
        COUNT(CASE WHEN A.is_prospect = '06' THEN A.qt_no END) AS pipeline,
        SUM(CASE WHEN A.is_prospect = '06' AND A.is_tracking IN ('1', '3') THEN A.so_amount END) AS pipeline_amount,
        SUM(CASE WHEN A.is_prospect = '06' AND A.is_tracking IN ('2', '4') THEN A.so_amount END) AS lost_pipeline_amount
    FROM cost_sheet_head A
    WHERE A.is_status <> 'C'
      AND YEAR(A.qt_date) = ?
      AND MONTH(A.qt_date) = ?
      AND A.is_tracking = ?
      AND NOT EXISTS (SELECT 1 FROM so_detail B WHERE A.qt_no = B.qt_no)
    GROUP BY FORMAT(A.qt_date, 'yyyy-MM')
    ORDER BY format_date ASC;
";
$params = array($year_no, $month_no, $track);

}else if($year_no <> 0 && $month_no != 'N' && $track == 'N' && $Sales != 'N'){
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,
A.appoint_no,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
    FROM cost_sheet_head A
    LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
    LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
    LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
    WHERE A.is_prospect <> '00' 
    AND is_status <> 'C'  
    AND B.so_no IS NULL
    AND YEAR(A.qt_date) = ?
    AND MONTH(A.qt_date) = ?
    AND staff_id = ?
    ORDER BY qt_date DESC";
$sqlrevenue = "SELECT 
     FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
     SUM(A.total_before_vat) AS so_amount,
     COUNT(A.so_no) AS so_no
     FROM 
     View_SO_SUM A
     WHERE 
     A.year_no = ?
     AND A.month_no = ?
     AND staff_id = ?
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
     qt_no IS NULL
     AND year_no = ? 
     AND month_no = ?
     AND staff_id = ?
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
 SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('1','3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
 SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('2','4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
 COUNT(CASE WHEN  is_prospect = '05' THEN A.qt_no END) AS potential,
 SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('1','3') THEN so_amount END) AS potential_amount,
 SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_potential_amount,
 COUNT(CASE WHEN  is_prospect = '04' THEN A.qt_no END) AS prospect,
 SUM  (CASE WHEN  is_prospect = '04'AND is_tracking IN ('1','3') THEN so_amount END) AS prospect_amount,
 SUM  (CASE WHEN  is_prospect = '04' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_prospect_amount,
 COUNT(CASE WHEN  is_prospect = '06' THEN A.qt_no END) AS pipeline,
 SUM  (CASE WHEN  is_prospect = '06'AND is_tracking IN ('1','3') THEN so_amount END) AS pipeline_amount,
 SUM  (CASE WHEN  is_prospect = '06' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_pipeline_amount
   FROM 
   cost_sheet_head A
   WHERE 
   is_status <> 'C' 
   AND YEAR(qt_date) = ? 
   AND MONTH(qt_date) = ? 
   AND staff_id = ?
   AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
   GROUP BY 
   FORMAT(qt_date, 'yyyy-MM')
   ORDER BY 
   format_date ASC";
    $params = array($year_no, $month_no, $Sales);
}else if($year_no <> 0 && $month_no == 'N' && $track != 'N' && $Sales != 'N'){
    $sqlappoint = "SELECT FORMAT(A.appoint_date, 'dd-MM-yyy') As appoint_date,
A.appoint_no,A.customer_name, A.qt_no,FORMAT(A.so_amount, 'N2') AS so_amount,pp.prospect_name,pp.prospect_code, A.remark,ms.status_name,ms.status_code,A.reasoning
    FROM cost_sheet_head A
    LEFT JOIN ms_appoint_status ms ON a.is_tracking = ms.status_code
    LEFT JOIN ms_prospect pp ON a.is_prospect = pp.prospect_code
    LEFT JOIN  so_customer_status B ON A.qt_no = B.qt_no
    WHERE A.is_prospect <> '00' 
    AND is_status <> 'C'  
    AND B.so_no IS NULL
    AND YEAR(A.qt_date) = ?
    AND A.is_tracking = ?
    AND A.staff_id = ?
    ORDER BY qt_date DESC";
$sqlrevenue = "SELECT 
     FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM') AS format_date,
     SUM(A.total_before_vat) AS so_amount,
     COUNT(A.so_no) AS so_no
     FROM 
     View_SO_SUM A
     WHERE 
     A.year_no = $year_no
     AND A.staff_id = $Sales
     GROUP BY 
     FORMAT(DATEFROMPARTS(A.year_no, A.month_no,1), 'yyyy-MM')
     ORDER BY 
     format_date ASC";
$sqlap = "SELECT 
     FORMAT(A.appoint_date, 'yyyy-MM') AS format_date,
     COUNT(CASE WHEN A.qt_no IS NULL AND A.is_status <> 4 THEN A.appoint_no END) AS appoint_no,
     COUNT(CASE WHEN A.qt_no IS NULL AND A.is_status = 4 THEN A.appoint_no END) AS specific_appoint_no
     FROM 
     appoint_head A
      LEFT JOIN 
    cost_sheet_head B ON A.appoint_no = B.appoint_no
     WHERE 
      B.appoint_no IS NULL 
     AND year_no = $year_no
     AND A.staff_id = $Sales
     GROUP BY 
     FORMAT(A.appoint_date, 'yyyy-MM')
     ORDER BY 
     format_date ASC";
$sqlcostsheet = "SELECT 
    FORMAT(qt_date, 'yyyy-MM') AS format_date,
    SUM(so_amount) AS so_amount,
    COUNT(A.qt_no) AS qt_no,
    COUNT(CASE WHEN is_prospect IS NULL THEN A.qt_no END) AS Unknownss,
    COUNT(CASE WHEN print_qt_count = 0 THEN A.qt_no END) AS Unknowns,
    COUNT(CASE WHEN is_prospect = '00' AND print_qt_count = 0 THEN A.qt_no END) AS Unknown,
    SUM(CASE WHEN is_prospect = '00' AND is_tracking IN ('1', '3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
    SUM(CASE WHEN is_prospect = '00' AND is_tracking IN ('2', '4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
    COUNT(CASE WHEN is_prospect = '05' THEN A.qt_no END) AS potential,
    SUM(CASE WHEN is_prospect = '05' AND is_tracking IN ('1', '3') THEN so_amount END) AS potential_amount,
    SUM(CASE WHEN is_prospect = '05' AND is_tracking IN ('2', '4') THEN so_amount END) AS lost_potential_amount,
    COUNT(CASE WHEN is_prospect = '04' THEN A.qt_no END) AS prospect,
    SUM(CASE WHEN is_prospect = '04' AND is_tracking IN ('1', '3') THEN so_amount END) AS prospect_amount,
    SUM(CASE WHEN is_prospect = '04' AND is_tracking IN ('2', '4') THEN so_amount END) AS lost_prospect_amount,
    COUNT(CASE WHEN is_prospect = '06' THEN A.qt_no END) AS pipeline,
    SUM(CASE WHEN is_prospect = '06' AND is_tracking IN ('1', '3') THEN so_amount END) AS pipeline_amount,
    SUM(CASE WHEN is_prospect = '06' AND is_tracking IN ('2', '4') THEN so_amount END) AS lost_pipeline_amount
FROM 
    cost_sheet_head A
WHERE 
    is_status <> 'C' 
    AND NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
    AND YEAR(qt_date) = ? 
    AND A.is_tracking = ? 
    AND staff_id = ?
GROUP BY 
    FORMAT(qt_date, 'yyyy-MM')
ORDER BY 
    format_date ASC;";
    $params = array($year_no, $track, $Sales);
}else{
    $sqlappoint = "SELECT 
        FORMAT(A.appoint_date, 'dd-MM-yyyy') AS appoint_date,
        A.appoint_no,
        A.customer_name,
        A.qt_no,
        FORMAT(A.so_amount, 'N2') AS so_amount,
        pp.prospect_name,
        pp.prospect_code,
        A.remark,
        ms.status_name,
        ms.status_code,
        A.reasoning
    FROM cost_sheet_head A
    LEFT JOIN ms_appoint_status ms ON A.is_tracking = ms.status_code
    LEFT JOIN ms_prospect pp ON A.is_prospect = pp.prospect_code
    LEFT JOIN so_customer_status B ON A.qt_no = B.qt_no
    WHERE 
        A.is_prospect <> '00'
        AND A.is_status <> 'C'
        AND B.so_no IS NULL
        AND YEAR(A.qt_date) = ?
        AND MONTH(A.qt_date) = ?
        AND A.is_tracking = ?
        AND A.staff_id = ?
    ORDER BY A.qt_date DESC;
";

$sqlrevenue = "SELECT 
        FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM') AS format_date,
        SUM(A.total_before_vat) AS so_amount,
        COUNT(A.so_no) AS so_no
    FROM View_SO_SUM A
    WHERE 
        A.year_no = $year_no
        AND A.month_no = $month_no
        AND A.staff_id = $Sales
    GROUP BY 
        FORMAT(DATEFROMPARTS(A.year_no, A.month_no, 1), 'yyyy-MM')
    ORDER BY format_date ASC;
";
$sqlap = "SELECT 
     FORMAT(A.appoint_date, 'yyyy-MM') AS format_date,
     COUNT(CASE WHEN A.qt_no IS NULL AND A.is_status <> 4 THEN A.appoint_no END) AS appoint_no,
     COUNT(CASE WHEN A.qt_no IS NULL AND A.is_status = 4 THEN A.appoint_no END) AS specific_appoint_no
     FROM 
     appoint_head A
      LEFT JOIN 
    cost_sheet_head B ON A.appoint_no = B.appoint_no
     WHERE 
      B.appoint_no IS NULL 
     AND year_no = $year_no
     AND month_no = $month_no
     AND A.staff_id = $Sales
     GROUP BY 
     FORMAT(A.appoint_date, 'yyyy-MM')
     ORDER BY 
     format_date ASC";
$sqlcostsheet = "SELECT 
 FORMAT(qt_date, 'yyyy-MM') AS format_date,
 SUM(so_amount)AS so_amount,
 COUNT(A.qt_no) AS qt_no,
 COUNT(CASE WHEN  is_prospect IS NULL    THEN A.qt_no END) AS Unknownss,
 COUNT(CASE WHEN  print_qt_count = 0   THEN A.qt_no END) AS Unknowns,
 COUNT(CASE WHEN  is_prospect = '00' AND print_qt_count = 0 THEN A.qt_no END) AS Unknown,
 SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('1','3') AND print_qt_count = 0 THEN so_amount END) AS Unknown_amount,
 SUM  (CASE WHEN  is_prospect = '00' AND is_tracking IN ('2','4') AND print_qt_count = 0 THEN so_amount END) AS lost_Unknown_amount,
 COUNT(CASE WHEN  is_prospect = '05' THEN A.qt_no END) AS potential,
 SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('1','3') THEN so_amount END) AS potential_amount,
 SUM  (CASE WHEN  is_prospect = '05' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_potential_amount,
 COUNT(CASE WHEN  is_prospect = '04' THEN A.qt_no END) AS prospect,
 SUM  (CASE WHEN  is_prospect = '04'AND is_tracking IN ('1','3') THEN so_amount END) AS prospect_amount,
 SUM  (CASE WHEN  is_prospect = '04' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_prospect_amount,
 COUNT(CASE WHEN  is_prospect = '06' THEN A.qt_no END) AS pipeline,
 SUM  (CASE WHEN  is_prospect = '06'AND is_tracking IN ('1','3') THEN so_amount END) AS pipeline_amount,
 SUM  (CASE WHEN  is_prospect = '06' AND is_tracking IN ('2','4') THEN so_amount END) AS lost_pipeline_amount
   FROM 
   cost_sheet_head A
   WHERE 
   is_status <> 'C' 
   AND YEAR(qt_date) = ? 
   AND MONTH(qt_date) = ? 
   AND A.is_tracking = ?
   AND A.staff_id = ?
   AND  NOT EXISTS (SELECT * FROM so_detail B WHERE A.qt_no = B.qt_no)
   GROUP BY 
   FORMAT(qt_date, 'yyyy-MM')
   ORDER BY 
   format_date ASC";
    $params = array($year_no, $month_no, $track, $Sales);
}
// First Query: Appoint Data
$stmt = sqlsrv_query($objCon, $sqlappoint, $params);
if ($stmt === false) {
    die(json_encode(["error" => sqlsrv_errors()]));
}

$tableData = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $tableData[] = $row;
}

// Second Query: AP Data
$stmtap = sqlsrv_query($objCon, $sqlap, $params);
if ($stmtap === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute AP query"]);
    exit;
}

$apData = [];
while ($row = sqlsrv_fetch_array($stmtap, SQLSRV_FETCH_ASSOC)) {
    $apData[] = $row;
}

// Third Query: Cost Sheet Data
$stmtqt = sqlsrv_query($objCon, $sqlcostsheet, $params);
if ($stmtqt === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute Cost Sheet query"]);
    exit;
}

$qtData = [];
while ($row = sqlsrv_fetch_array($stmtqt, SQLSRV_FETCH_ASSOC)) {
    $qtData[] = $row;
}

// Fourth Query: Revenue Data
$stmt1 = sqlsrv_query($objCon, $sqlrevenue, $params);
if ($stmt1 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute Revenue query"]);
    exit;
}

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
