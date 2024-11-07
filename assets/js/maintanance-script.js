function fetchYear() {
const year_no = document.getElementById('year').value;
const month_no = document.getElementById('month').value;
let url;

  url = `maintanance.php?year_no=${year_no}&month_no=${month_no}`;
  

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
  BarChart(data);
  updateReport(data);

})
.catch(error => console.error('Error fetching data:', error));

}


function updateTable(data) {
        let totalSum = 0;
        let totalSumbox = 0;
        let totalSum1 = 0;
        let totalSum2 = 0;
        let totalSum3 = 0;
        let totalSum4 = 0;
        let totalSum5 = 0;
        let totalSum6 = 0;
        data.boxData.forEach(box => {
            totalSum += parseFloat(box.ct_amount) || 0;
            totalSumbox += parseFloat(box.CT) || 0;
            totalSum1 += parseFloat(box.tp_amount)|| 0;
            totalSum2 += parseFloat(box.TP)|| 0;
            totalSum3 += parseFloat(box.oc_amount)|| 0;
            totalSum4 += parseFloat(box.OC)|| 0;
            totalSum5 += parseFloat(box.cl_amount)|| 0;
            totalSum6 += parseFloat(box.CL)|| 0;
        });
          
          const revenueElement = document.getElementById('container_value');
          revenueElement.textContent = totalSum.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
          }); 
  
          const countElement2 = document.getElementById('container_number');
          countElement2.textContent = totalSumbox.toLocaleString('en-US', {
          });  
  
        const Element = document.getElementById('tp_value');
        Element.textContent = totalSum1.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const countElement = document.getElementById('tp_number');
        countElement.textContent = totalSum2.toLocaleString('en-US', {
        }) ; 

        const Element1 = document.getElementById('oc_value');
        Element1.textContent = totalSum3.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const countElement1 = document.getElementById('oc_number');
        countElement1.textContent = totalSum4.toLocaleString('en-US', {
        }) ; 

        const Element2 = document.getElementById('cl_value');
        Element2.textContent = totalSum5.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const countElement3 = document.getElementById('cl_number');
        countElement3.textContent = totalSum6.toLocaleString('en-US', {
        }) ; 
/*
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
  });*/
    }
    
    
  
    //*****************************pie segment chart ***************************************************//
 
    function BarChart(data) {
      let names = [];
      let values = [];
    
      // Populate arrays from graphData
      data.graphpieData.forEach(bar => {
        names.push(bar.repair_name);  // Categories (names) for x-axis
        values.push(bar.total_amount); // Values for the bars
      });
      echarts.init(document.querySelector("#barChart")).setOption({
        title: {
          text: 'List'
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'shadow'
          }
        },
        legend: {},
        grid: {
          left: '2%',
          right: '4%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: {
          type: 'value',
          boundaryGap: [0, 0.01]
        },
        yAxis: {
          type: 'category',
          data: names
        },
        series: [{
            name: names,
            type: 'bar',
            data: values
          }  
        ]
      });

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
function updateReport(data) {
  const formatDate = data.graphData.map(item => item.format_date);
  const total_amount_2024 = data.graphData.map(item => item.total_amount1);
  const total_ALL = data.graphData.map(item => item.total_amount);
  const monthNames = [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun", 
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
  ];
  const target = data.graphData.map(item => item.target_ma);
 
  new ApexCharts(document.querySelector("#reportsChart"), {
    series: [/*{
      name: 'Appoints',
      data: appoints,
    }, */{
      name: 'เป้าหมายค่าซ่อม',
      data: target,
    }, {
      name: 'ค่าซ่อมที่เกิดตามเป้า',
      data: total_amount_2024,
    },{
      name: 'ค่าซ่อมทั้งหมด',
      data: total_ALL,
    }],
    chart: {
      height: 350,
      type: 'bar',
      toolbar: {
        show: false
      },
    },
    markers: {
      size: 4
    },
    colors: ['#0d6efd', '#ff771d', '#2eca6a'],
    fill: {
      type: "gradient",
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.3,
        opacityTo: 0.4,
        stops: [0, 90, 100]
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth',
      width: 2
    },
    xaxis: {
      type: 'category',
      categories: formatDate
    },
    tooltip: {
      y: {
        formatter: function(value) {
          return value.toLocaleString(undefined, { style: 'currency', currency: 'THB' });
        },
      },
    }
  }).render();
}
