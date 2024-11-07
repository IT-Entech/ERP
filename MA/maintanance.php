<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('C:\xampp\htdocs\connectDB\connectDB.php');
$objCon = connectDB(); // Connect to the database

if ($objCon === false) {
    die(print_r(sqlsrv_errors(), true));
}

$currentYear = date("Y");
$currentMonth = date("m");
$year_no = isset($_GET['year_no']) ? $_GET['year_no'] : $currentYear;
$month_no = isset($_GET['month_no']) ? $_GET['month_no'] : $currentMonth;

if ($year_no <> 0 && $month_no == 0) {
    $sqlbox = "		 WITH MA AS(
					SELECT 
					 CASE WHEN container_type_code != '00' THEN COUNT(A.container_type_code)
					 END AS CT,
					 /*(SELECT size_no FROM ms_container B WHERE A.container_code = B.container_code)AS size_no,
					(SELECT number_no FROM ms_container B WHERE A.container_code = B.container_code)AS number_no,*/
					CASE WHEN container_type_code != '00' THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS ct_amount,
					 CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code NOT IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061','บธ 1608','ณค 3475') THEN COUNT(vehicle_code)
					 END AS TP,
					 CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code NOT IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061','บธ 1608','ณค 3475') THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS 'tp_amount',
					  CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061') THEN COUNT(vehicle_code)
					 END AS OC,
					 CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061') THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS 'oc_amount',
					  CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('บธ 1608','ณค 3475') THEN COUNT(vehicle_code)
					 END AS CL,			 
              			CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('บธ 1608','ณค 3475') THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS 'cl_amount'
              		  FROM repair_head A
					  LEFT JOIN ms_repair_type msr ON A.repair_type_code = msr.repair_type_code
              		  LEFT JOIN (
              			SELECT A.repair_no, SUM(B.total_amount) AS stock_amount
              			FROM brw_head A
              			LEFT JOIN brw_detail B ON A.brw_no = B.brw_no
              			WHERE A.is_status <> 'C' AND A.stock_code IN ('02', '06')
              			GROUP BY A.repair_no
              		  ) brw_summary ON A.repair_no = brw_summary.repair_no
              		  WHERE A.is_status <> 'C' 
              		  AND YEAR(A.repair_date) = ?
              		  GROUP BY A.vehicle_code,container_type_code,container_code
				  		)
		SELECT  
		SUM(CT) AS CT,
		SUM(ct_amount) AS ct_amount,
		SUM(TP) AS TP,
		SUM(tp_amount) AS tp_amount,
		SUM(OC) AS OC,
		SUM(oc_amount) AS oc_amount,
		SUM(CL) AS CL,
		SUM(cl_amount) AS cl_amount
		FROM MA";
     $sqlvehicle = "	SELECT 
              		FORMAT(A.repair_date, 'yyyy-MM') AS format_date, 
              		COUNT(A.total_before_vat)AS vehicle_code, 
              		SUM(COALESCE(pr_summary.pr_amount, 0)) AS pr_amount, 
              		SUM(COALESCE(brw_summary.stock_amount, 0)) AS stock_amount,
              		SUM(COALESCE(pr_summary.pr_amount, 0) + COALESCE(brw_summary.stock_amount, 0)) AS total_amount
              	  FROM repair_head A
              	  LEFT JOIN (
              		SELECT repair_no, SUM(B.total_amount) AS pr_amount
              		FROM pr_head A
              		LEFT JOIN pr_detail B ON A.pr_no = B.pr_no
              		WHERE A.is_status <> 'C' 
              		GROUP BY A.repair_no
              	  ) pr_summary ON A.repair_no = pr_summary.repair_no
              	  LEFT JOIN (
              		SELECT A.repair_no, SUM(B.total_amount) AS stock_amount
              		FROM brw_head A
              		LEFT JOIN brw_detail B ON A.brw_no = B.brw_no
              		WHERE A.is_status <> 'C' AND A.stock_code IN ('02', '06')
              		GROUP BY A.repair_no
              	  ) brw_summary ON A.repair_no = brw_summary.repair_no
              	  WHERE A.is_status <> 'C' 
              	  AND YEAR(A.repair_date) = ? AND vehicle_code IS NOT NULL
              	  GROUP BY 
              	  FORMAT(A.repair_date, 'yyyy-MM')";  
    $sqlgraphpie = " WITH MA AS(
SELECT 
              	CASE WHEN A.repair_type_code = '000' THEN 'Container'
		WHEN A.repair_type_code != '000' THEN msr.repair_type_name
		END AS repair_name,
              		SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0)) AS total_amount
              	  FROM repair_head A
				  LEFT JOIN ms_repair_type msr ON A.repair_type_code = msr.repair_type_code
              	  LEFT JOIN (
              		SELECT A.repair_no, SUM(B.total_amount) AS stock_amount
              		FROM brw_head A
              		LEFT JOIN brw_detail B ON A.brw_no = B.brw_no
              		WHERE A.is_status <> 'C' AND A.stock_code IN ('02', '06')
              		GROUP BY A.repair_no
              	  ) brw_summary ON A.repair_no = brw_summary.repair_no
              	  WHERE A.is_status <> 'C' 
              	  AND YEAR(A.repair_date) = ?
              	  GROUP BY A.repair_type_code, msr.repair_type_name
				  	)
	        SELECT  * FROM MA
		ORDER BY total_amount DESC";        
    $params = array($year_no);
}else{
                $sqlbox = "		 WITH MA AS(
					SELECT 
					 CASE WHEN container_type_code != '00' THEN COUNT(A.container_type_code)
					 END AS CT,
					 /*(SELECT size_no FROM ms_container B WHERE A.container_code = B.container_code)AS size_no,
					(SELECT number_no FROM ms_container B WHERE A.container_code = B.container_code)AS number_no,*/
					CASE WHEN container_type_code != '00' THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS ct_amount,
					 CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code NOT IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061','บธ 1608','ณค 3475') THEN COUNT(vehicle_code)
					 END AS TP,
					 CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code NOT IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061','บธ 1608','ณค 3475') THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS 'tp_amount',
					  CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061') THEN COUNT(vehicle_code)
					 END AS OC,
					 CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('ญฐ3356','ฆฒ 8571','ฌถ-7644','ณย 9130','ฌถ 9061') THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS 'oc_amount',
					  CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('บธ 1608','ณค 3475') THEN COUNT(vehicle_code)
					 END AS CL,			 
              			CASE WHEN container_type_code = '00' AND vehicle_code IS NOT NULL AND vehicle_code IN ('บธ 1608','ณค 3475') THEN SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0))END  AS 'cl_amount'
              		  FROM repair_head A
					  LEFT JOIN ms_repair_type msr ON A.repair_type_code = msr.repair_type_code
              		  LEFT JOIN (
              			SELECT A.repair_no, SUM(B.total_amount) AS stock_amount
              			FROM brw_head A
              			LEFT JOIN brw_detail B ON A.brw_no = B.brw_no
              			WHERE A.is_status <> 'C' AND A.stock_code IN ('02', '06')
              			GROUP BY A.repair_no
              		  ) brw_summary ON A.repair_no = brw_summary.repair_no
              		  WHERE A.is_status <> 'C' 
              		  AND YEAR(A.repair_date) = ? AND MONTH(A.repair_date) = ?
              		  GROUP BY A.vehicle_code,container_type_code,container_code
				  		)
		SELECT  
		SUM(CT) AS CT,
		SUM(ct_amount) AS ct_amount,
		SUM(TP) AS TP,
		SUM(tp_amount) AS tp_amount,
		SUM(OC) AS OC,
		SUM(oc_amount) AS oc_amount,
		SUM(CL) AS CL,
		SUM(cl_amount) AS cl_amount
		FROM MA
";
$sqlvehicle = "	SELECT 
                FORMAT(A.repair_date, 'yyyy-MM') AS format_date, 
                COUNT(A.total_before_vat)AS vehicle_code, 
                SUM(COALESCE(pr_summary.pr_amount, 0)) AS pr_amount, 
                SUM(COALESCE(brw_summary.stock_amount, 0)) AS stock_amount,
                SUM(COALESCE(pr_summary.pr_amount, 0) + COALESCE(brw_summary.stock_amount, 0)) AS total_amount
                FROM repair_head A
                LEFT JOIN (
                SELECT repair_no, SUM(B.total_amount) AS pr_amount
                FROM pr_head A
                LEFT JOIN pr_detail B ON A.pr_no = B.pr_no
                WHERE A.is_status <> 'C' 
                GROUP BY A.repair_no
                ) pr_summary ON A.repair_no = pr_summary.repair_no
                LEFT JOIN (
                SELECT A.repair_no, SUM(B.total_amount) AS stock_amount
                FROM brw_head A
                LEFT JOIN brw_detail B ON A.brw_no = B.brw_no
                WHERE A.is_status <> 'C' AND A.stock_code IN ('02', '06')
                GROUP BY A.repair_no
                ) brw_summary ON A.repair_no = brw_summary.repair_no
                WHERE A.is_status <> 'C' 
                AND YEAR(A.repair_date) = ? AND MONTH(A.repair_date) = ? 
                GROUP BY 
                FORMAT(A.repair_date, 'yyyy-MM')";  
  $sqlgraphpie = "	 WITH MA AS(
SELECT 
              	CASE WHEN A.repair_type_code = '000' THEN 'Container'
		WHEN A.repair_type_code != '000' THEN msr.repair_type_name
		END AS repair_name,
              		SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0)) AS total_amount
              	  FROM repair_head A
				  LEFT JOIN ms_repair_type msr ON A.repair_type_code = msr.repair_type_code
              	  LEFT JOIN (
              		SELECT A.repair_no, SUM(B.total_amount) AS stock_amount
              		FROM brw_head A
              		LEFT JOIN brw_detail B ON A.brw_no = B.brw_no
              		WHERE A.is_status <> 'C' AND A.stock_code IN ('02', '06')
              		GROUP BY A.repair_no
              	  ) brw_summary ON A.repair_no = brw_summary.repair_no
              	  WHERE A.is_status <> 'C' 
              	  AND YEAR(A.repair_date) = ? AND MONTH(A.repair_date) = ?
              	  GROUP BY A.repair_type_code, msr.repair_type_name
				  	)
	        SELECT  * FROM MA
		ORDER BY total_amount DESC";  
    $params = array($year_no, $month_no);
}
$sqlgraph = "SELECT 
    FORMAT(A.repair_date, 'MMM') AS format_date, 
    MONTH(A.repair_date) AS month_number,
    SUM(CASE 
        WHEN A.repair_type_code NOT IN ('001', '002') AND A.vehicle_code IS NOT NULL 
        THEN COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0)
        ELSE 0 
    END) AS total_amount1,
    SUM(COALESCE(A.total_before_vat, 0) + COALESCE(brw_summary.stock_amount, 0)) AS total_amount,
    '160000.0000' AS target_ma
FROM repair_head A
LEFT JOIN (
    SELECT A.repair_no, SUM(B.total_amount) AS stock_amount
    FROM brw_head A
    LEFT JOIN brw_detail B ON A.brw_no = B.brw_no
    WHERE A.is_status <> 'C' AND A.stock_code IN ('02', '06')
    GROUP BY A.repair_no
) brw_summary ON A.repair_no = brw_summary.repair_no
WHERE A.is_status <> 'C' 
AND YEAR(A.repair_date) = 2024 
GROUP BY 
    FORMAT(A.repair_date, 'MMM'), 
    MONTH(A.repair_date)
ORDER BY 
    month_number"; 
// Execute the first query
$stmt = sqlsrv_query($objCon, $sqlbox, $params);
if ($stmt === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

$stmt1 = sqlsrv_query($objCon, $sqlvehicle, $params);
if ($stmt1 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

$stmt2 = sqlsrv_query($objCon, $sqlgraph);
if ($stmt2 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

$stmt3 = sqlsrv_query($objCon, $sqlgraphpie, $params);
if ($stmt3 === false) {
    $errors = sqlsrv_errors();
    error_log(print_r($errors, true)); // Log SQL errors for debugging
    http_response_code(500); // Set HTTP status code to indicate internal server error
    echo json_encode(["error" => "Failed to execute first query"]);
    exit;
}

// Initialize an array to hold the first query results
$boxData = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $boxData[] = $row;
}
sqlsrv_free_stmt($stmt);

$vehicleData = [];
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $vehicleData[] = $row;
}
sqlsrv_free_stmt($stmt1);

$graphData = [];
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $graphData[] = $row;
}
sqlsrv_free_stmt($stmt2);

$graphpieData = [];
while ($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
    $graphpieData[] = $row;
}
sqlsrv_free_stmt($stmt3);


// Close the database connection
sqlsrv_close($objCon);

$data = [
    'boxData' => $boxData,
    'vehicleData' => $vehicleData,
    'graphData' => $graphData,
    'graphpieData' => $graphpieData
];

header('Content-Type: application/json');
echo json_encode($data);

?>
