function fetchYear() {
const year_no = document.getElementById('year').value;
const month_no = document.getElementById('month').value;
const channel = document.getElementById('channel').value;
const Sales = document.getElementById('Sales').value;
const is_new = document.getElementById('is_new').value;
let url;

  url = `revenue.php?year_no=${year_no}&month_no=${month_no}&channel=${channel}&Sales=${Sales}&is_new=${is_new}`;
  

fetch(url)
.then(response => {
  if (!response.ok) {
    throw new Error('Network response was not ok');
  }
  return response.json();
})
.then(data => {
  console.log('Data:', data); // Log the data to check the response
  updateTable(data);
  updateChart(data.segmentData);

})
.catch(error => console.error('Error fetching data:', error));

}
function fetchData(period) {
  const year_no = document.getElementById('year').value;
const month_no = document.getElementById('month').value;
const channel = document.getElementById('channel').value;
const Sales = document.getElementById('Sales').value;
const is_new = document.getElementById('is_new').value;
  let url;
  url = `revenue.php?year_no=${year_no}&month_no=${month_no}&channel=${channel}&Sales=${Sales}&is_new=${is_new}`;

  fetch(url, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'period=' + period
  })
  .then(response => response.json()) // Assuming the response is JSON
  .then(data => {
      console.log(data); // Handle the response data (example: log the data)
      document.getElementById('dataContainer').innerHTML = data.content; // Update the DOM
  })
  .catch(error => console.error('Error fetching data:', error));
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
              Ratio: ${percentage} %<br>
              Value: ${formattedValue}<br>
              AOV: ${aov}<br>
              winrate: ${aov} %
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
    

    /*function BarChart(RegionData) {
      const regionCategories = ['North', 'Central', 'East', 'North-East', 'West', 'South'];
      const Data = RegionData.map(item => ({
        name: item.segment,
        data: regionCategories.map(region => item[region] || 0) // Ensure data is an array of region counts
    }));
    
      const chart = new ApexCharts(document.querySelector("#columnChart"), {
        chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: regionCategories,
        },
        yaxis: {
          title: {
            text: 'Customers'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + " customers";
            }
          }
        },
        legend: {
          top: '5%',
          left: 'center'
        },
        series: Data
      });
    
      chart.render();
    }*/
    document.addEventListener('DOMContentLoaded', fetchYear);

    document.addEventListener('DOMContentLoaded', (event) => {
      fetch('staff_id.php')
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
                  option.textContent = item.fname_e || item.nick_name || item.staff_id; 
                  selectElement.appendChild(option);
              });
          })
          .catch(error => console.error('Error fetching data:', error));
  });

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

