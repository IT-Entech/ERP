// Function to fetch session data from header.php
function getSessionData() {
  fetch('../header.php')
    .then(response => response.json()) // Parse the JSON from the response
    .then(data => {
      //console.log('Session Data:', data);

      const { name, staff, level, role, position } = data;
      if (staff == 0 || level <= 1) {
        alert("Cannot enter this site.");
        window.location = "/pages-login.html";
        return;
      }
      // Conditionally show Maintenance and Permission nav items
      if (level == 2 || level == 3) {
        toggleMaintenanceNav(true);

      // Fetch staff data if needed for select options
      fetch('/ERP/staff_id.php')
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          const selectElement = document.getElementById('Sales');
          data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.staff_id;
            option.textContent = item.fname_e || item.nick_name;
            selectElement.appendChild(option);
          });
        })
        .catch(error => console.error('Error fetching staff data:', error));
      }
      // Update hidden fields and display the user name
      document.getElementById('fetch-level').value = level;
      document.getElementById('name-display').textContent = name;
      document.getElementById('name-display1').textContent = name;
      document.getElementById('position-name').textContent = position;
      document.getElementById('fetch-staff').value = staff;

      // Now call fetchYear() to fetch year-based data
      fetchYear(); // Ensure session data is available before fetching year data
    })
    .catch(error => {
      console.error('Error fetching session data:', error);
    });
}

// Call the function to fetch session data
getSessionData();
function toggleMaintenanceNav(isVisible) {
  var maintenanceNav = document.getElementById('maintanance-nav');
  var permissionNav = document.getElementById('permission-nav');
  var selectSale = document.getElementById('select-sale');
  if (isVisible) {
    maintenanceNav.classList.remove('d-none'); // Show the item
    permissionNav.classList.remove('d-none');
    selectSale.classList.remove('d-none');
  } else {
    maintenanceNav.classList.add('d-none');    // Hide the item
    permissionNav.classList.add('d-none');
    selectSale.classList.add('d-none');
  }
}
// Fetch year data and update the dashboard based on the selected values
function fetchYear() {
  const level = document.getElementById('fetch-level').value;
  const year_no = document.getElementById('year').value;
  const month_no = document.getElementById('month').value;
  const is_new = document.getElementById('is_new').value;
  const user = document.getElementById('fetch-staff').value;  // Ensure this value is populated before fetching
  const channel = document.getElementById('channel').value;

  // Use the passed element to get the segment number
  const segment = '999';  // Default value for segment
  
  let Sales;
  if (level == 1) {
    Sales = user;
  } else if (level == 2 || level == 3) {
    Sales = document.getElementById('Sales').value;
  }

  // Construct URLs for fetching the dashboard and report chart data
  const url = `./fetch-dashboard.php?year_no=${year_no}&month_no=${month_no}&channel=${channel}&Sales=${Sales}&is_new=${is_new}`;
  const url1 = `./reportchart.php?year_no=${year_no}&segment=${segment}&Sales=${Sales}&is_new=${is_new}&channel=${channel}`;
  
  console.log('Fetching data from URL:', url1);

  // Fetch dashboard data
  fetch(url)
    .then(response => {
      if (!response.ok) {
        throw new Error(`Error fetching dashboard data: ${response.statusText}`);
      }
      return response.json();
    })
    .then(data => {
      console.log('Dashboard Data:', data);  // Log the data to check the response
      updateTable(data);  // Function to update the table with dashboard data
      updateChart(data.segmentData); // Function to update the chart with segment data from dashboard
    })
    .catch(error => console.error('Error fetching dashboard data:', error));

  // Fetch report chart data
  fetch(url1)
    .then(response => {
      if (!response.ok) {
        throw new Error(`Error fetching report chart data: ${response.statusText}`);
      }
      return response.json();
    })
    .then(data1 => {
      console.log('Report Chart Data:', data1);  // Log the data to check the response
      updateReport(data1);
    })
    .catch(error => console.error('Error fetching report chart data:', error));
}

function updateTable(data) {
        let totalSum = 0;
        let totalSumso = 0;
        data.revenueData.forEach(revenue => {
            totalSum += parseFloat(revenue.so_amount)|| 0;
            totalSumso += parseFloat(revenue.so_no)|| 0;
        });

        const revenueElement = document.getElementById('revenue');
        revenueElement.textContent = totalSum.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const countElement2 = document.getElementById('so_number');
        countElement2.textContent = totalSumso.toLocaleString('en-US', {
        });  


        let totalSum1 = 0;
        let totalSumqt = 0;
        data.costsheetData.forEach(qt => {
          totalSum1 += parseFloat(qt.so_amount)|| 0;
          totalSumqt += parseFloat(qt.qt_no)|| 0;
        });
        const qtElement = document.getElementById('qt_value');
        qtElement.textContent = totalSum1.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 


        const countElement1 = document.getElementById('qt_number');
        countElement1.textContent = totalSumqt.toLocaleString('en-US', {
        }) ; 

       
        let totalSumap = 0;
        let totalSumap_quality = 0;
        data.appointData.forEach(ap => {
          totalSumap += parseFloat(ap.appoint_no) || 0;
          totalSumap_quality += parseFloat(ap.appoint_quality) || 0;
        });
        const countElement = document.getElementById('appoint');
        countElement.textContent = totalSumap.toLocaleString('en-US', {
        }); 

        const countElementAP = document.getElementById('ap_quality');
        countElementAP.textContent = totalSumap_quality.toLocaleString('en-US', {
        }); 


        let totalSum3 = 0;
        data.orderData.forEach(or => {
          totalSum3 += parseFloat(or.order_no) || 0;
        });
        const orElement1 = document.getElementById('or_number');
        orElement1.textContent = totalSum3.toLocaleString('en-US', {
        });   

        let totalSum2 = 0;
        data.orderData.forEach(or => {
          totalSum2 += parseFloat(or.order_amount);
        });
        const orElement = document.getElementById('order_est');
        orElement.textContent = totalSum2.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

                // Calculate and display the ratio (revenue per sales order)
        const winrate = totalSumso || 0;
        const winrateElement = document.getElementById('winrate');
        winrateElement.textContent = winrate.toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        const winrateP = (totalSumso / totalSumqt) * 100 || 0;
        const winratePElement = document.getElementById('winrate_percent');
        winratePElement.textContent = winrateP.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' %';
                // Calculate and display the ratio (revenue per sales order)
const ratio = totalSum / totalSumso || 0;
const ratioElement = document.getElementById('AOV');
ratioElement.textContent = ratio.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
});

const percentage = (ratio / totalSum) * 100 || 0;
const percentageElement = document.getElementById('AOV_percent');
percentageElement.textContent = percentage.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
}) + ' %';
const tbody = document.querySelector('#region tbody');
  tbody.innerHTML = '';

  data.regionData.forEach((row, index) => {
    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td>${row.segment}</td>
      <td>${row.North}</td>
      <td>${row.Central}</td>
      <td>${row.East}</td>
      <td>${row.North_East}</td>
      <td>${row.West}</td>
      <td>${row.South}</td>
    `;

    tbody.appendChild(tr);
  });
    }
    
    
  
    //*****************************pie segment chart ***************************************************//
    function updateChart(segmentData) {
      // Prepare chart data with segment_count as the value for the pie chart
      const chartData = segmentData.map(item => ({
        value: item.segment_count, // This will be the displayed value in the pie chart
        name: item.customer_segment_name, // Segment name for the pie slices
        total_before_vat: item.total_before_vat, // Include total_before_vat for the tooltip
        aov: item.aov
      }));
    
      // Initialize chart on the element with ID 'trafficChart'
      const chart = echarts.init(document.querySelector("#trafficChart"));
    
      // Set chart options
      chart.setOption({
        tooltip: {
          trigger: 'item',
          formatter: function (params) {
 // Format total_before_vat with commas and two decimal places
 const formattedValue = params.data.total_before_vat.toLocaleString('en-US', {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2
});
const aov = params.data.aov;

// Calculate the percentage of the segment
const percentage = params.percent.toFixed(2);
            return `
              <b>${params.name}</b><br>
              Product qty: ${params.value}<br>
              Winrate: ${percentage} %<br>
              Value: ${formattedValue}<br>
              AOV: ${aov}
            `;
          }
        },
        legend: {
          top: '5%',
          left: 'center'
        },
        series: [{
          name: 'Product',
          type: 'pie',
          radius: ['40%', '70%'],
          avoidLabelOverlap: false,
          label: {
            show: false,
            position: 'center'
          },
          emphasis: {
            label: {
              show: true,
              fontSize: '18',
              fontWeight: 'bold'
            }
          },
          labelLine: {
            show: false
          },
          data: chartData // Use the prepared chartData
        }]
      });
    }
    let chart;
    function updateReport(data1) {
      const segment01 = data1.graphData.map(item => item.product_so);
      const segment02 = data1.graphData.map(item => item.product_so2);
      const segment03 = data1.graphData.map(item => item.product_so3);
      const segment04 = data1.graphData.map(item => item.product_so4);
      const segment99 = data1.graphData.map(item => item.product_so99);
      const target_revenue = data1.graphData.map(item => item.accumulated_target);
      const saleorderAccu = data1.graphData.map(item => parseFloat(item.accumulated_so).toFixed(0));
      const dateAP = data1.graphData.map(item => item.format_date);
    
      new Chart(document.querySelector('#stakedBarChart'), {
        type: 'bar',
        data: {
          labels: dateAP,
          datasets: [{
              label: 'กากตะกอน',
              data: segment01,
              backgroundColor: 'rgb(255, 99, 132)',
            },
            {
              label: 'Product offSpec',
              data: segment02,
              backgroundColor: 'rgb(75, 192, 195)',
            },
            {
              label: 'HAZ',
              data: segment03,
              backgroundColor: '#6610f2',
            },
            {
              label: 'Cleaning',
              data: segment04,
              backgroundColor: 'rgb(255, 205, 86)',
            },
            {
              label: 'อื่นๆ',
              data: segment99,
              backgroundColor: '#20c997',
            },
            {
              label: 'ยอดขายสะสม',
              data: saleorderAccu,  // Example data for line chart
              type: 'line',  // Define this dataset as a line chart
              borderColor: 'rgb(54, 162, 235)', // Line color
              fill: false,  // No fill below the line
              tension: 0.3,  // Smooth curve
              //yAxisID: 'y-line', 
            }
          ]
        },
        options: {
          plugins: {
            title: {
              display: true,
              text: 'ยอดขายรวมประจำปี แยกตามProduct'
            },
          },
          responsive: true,
          scales: {
            x: {
              stacked: true,
            },
            y: {
              stacked: true,
          
              ticks: {
                beginAtZero: true,
                callback: function(value) {
                  return value.toLocaleString(); // Format y-axis values
                }
            }
          }
          }
        }
      });
      // Check if the chart is initialized
      /*if (!chart) {
          chart = new ApexCharts(document.querySelector("#reportsChart"), {
              series: [{
                  name: 'Target',
                  data: target_revenue,
              }, {
                  name: 'Revenue',
                  data: saleorderAccu,
              }],
              chart: {
                type: 'area',
                height: 350,
                zoom: {
                  enabled: false
                }
              },
              markers: {
                  size: 4
              },
              colors: ['#0d6efd', '#2eca6a'],
              dataLabels: {
                  enabled: false
              },
              stroke: {
                  curve: 'straight',
                  width: 2
              },
              subtitle: {
                  text: 'Revenue Movement',
                  align: 'left'
              },
              xaxis: {
                  type: 'category',
                  categories: dateAP
              },
              yaxis: {
                  opposite: true,
                  labels: {
                    formatter: function(value) {
                        return value.toLocaleString(undefined, { style: 'currency', currency: 'THB' });
                    }
                }
              },
              tooltip: {
                  y: {
                      formatter: function(value) {
                          return value.toLocaleString(undefined, { style: 'currency', currency: 'THB' });
                      }
                  }
              }
          });
          chart.render();
      } else {
          // Update the existing chart
          chart.updateSeries([{
              name: 'Target',
              data: target_revenue,
          }, {
              name: 'Revenue',
              data: saleorderAccu,
          }]);
          chart.updateOptions({
              xaxis: {
                  categories: dateAP
              }
          });
      }*/
    }
    

    document.addEventListener('DOMContentLoaded', fetchYear);


  const monthSelect = document.getElementById('month');
  const monthNames = [
    "January", "February", "March", "April", "May", "June", 
    "July", "August", "September", "October", "November", "December"
  ];
  
  monthNames.forEach((month, index) => {
    const option = document.createElement('option');
    option.value = index + 1; // 1 for January, 2 for February, etc.
    option.text = month;
    monthSelect.appendChild(option);
  });
  
  // Optionally, set the current month as the selected option
  const currentMonth = new Date().getMonth() + 1;
  monthSelect.value = currentMonth;
  
  const yearSelect = document.getElementById('year');
const currentYear = new Date().getFullYear();
const startYear = 2023;

for (let year = currentYear; year >= startYear; year--) {
  const option = document.createElement('option');
  option.value = year;
  option.text = year;
  yearSelect.appendChild(option);
}
/*document.addEventListener("DOMContentLoaded", () => {
  const data1 = { 
    graphData: [
      { accumulated_so: 100 }, 
      { accumulated_so: 200 }, 
      { accumulated_so: 300 }
    ]
  };
  const saleorderAccu = data1.graphData.map(item => parseFloat(item.accumulated_so).toFixed(0));
  new Chart(document.querySelector('#stakedBarChart'), {
    type: 'bar',
    data: {
      labels: monthNames,
      datasets: [{
          label: 'กากตะกอน',
          data: saleorderAccu,
          backgroundColor: 'rgb(255, 99, 132)',
        },
        {
          label: 'Cleaning',
          data: [11, 5, 12, 62, 95],
          backgroundColor: 'rgb(75, 192, 192)',
        },
        {
          label: 'Dataset 3',
          data: [44, 5, 22, 35, 62],
          backgroundColor: 'rgb(255, 205, 86)',
        },
        {
          label: 'ยอดขายสะสม',
          data: [155  , 210, 310, 100, 100],  // Example data for line chart
          type: 'line',  // Define this dataset as a line chart
          borderColor: 'rgb(54, 162, 235)', // Line color
          height: 350,
          fill: true,  // No fill below the line
          tension: 0.3,  // Smooth curve
          yAxisID: 'y-line', 
          stroke: {
            curve: 'straight',
            width: 2
        },
        }
      ]
    },
    options: {
      plugins: {
        title: {
          display: true,
          text: 'ยอดขายรวมประจำปี แยกตามProduct'
        },
      },
      responsive: true,
      scales: {
        x: {
          stacked: true,
        },
        y: {
          stacked: true
        },
        'y-line': {  // Define a second y-axis for the line chart
          type: 'linear',
          position: 'right',
          ticks: {
            beginAtZero: true,
            callback: function(value) {
              return value.toLocaleString(); // Format y-axis values
            }
          }
        }
      }
    }
  });
});*/

