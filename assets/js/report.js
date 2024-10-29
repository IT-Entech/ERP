function fetchYear() {
  const year_no = document.getElementById('year').value;
  const month_no = document.getElementById('month').value;
const channel = document.getElementById('channel').value;
const Sales = document.getElementById('Sales').value;
const is_new = document.getElementById('is_new').value;
  let url = `reportchart.php?year_no=${year_no}&month_no=${month_no}&channel=${channel}&Sales=${Sales}&is_new=${is_new}`;

  fetch(url)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(report => {
      console.log('Data:', report); // Log the data to check the response
      updateReport(report);
    })
    .catch(error => console.error('Error fetching data:', error));
}   

function updateReport(report) {
  const appoints = report.APData.map(item => item.appoint_no);
  const costsheet = report.QTData.map(item => item.qt_no);
  const saleorder = report.SOData.map(item => item.so_no);
  const dateAP = report.APData.map(item => item.format_date);

  new ApexCharts(document.querySelector("#reportsChart"), {
    series: [{
      name: 'Appoints',
      data: appoints,
    }, {
      name: 'Quotation',
      data: costsheet,
    }, {
      name: 'Revenue',
      data: saleorder,
    }],
    chart: {
      height: 350,
      type: 'area',
      toolbar: {
        show: false
      },
    },
    markers: {
      size: 4
    },
    colors: ['#ff771d', '#4154f1', '#2eca6a'],
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
      categories: dateAP
    },
    tooltip: {
      x: {  
        format: 'MM/yyyy'
      },
    }
  }).render();
}
  document.addEventListener('DOMContentLoaded', fetchYear);