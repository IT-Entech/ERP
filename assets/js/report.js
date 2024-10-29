function fetchProduct(element) {
  if (!element) {
    console.error('No element was passed to fetchProduct.');
    return;
  }

  // Retrieve the data-segment-no from the clicked <a> element
  const segment = element.getAttribute('data-segment-no') || '999';
  const Sales = document.getElementById('Sales').value;
  const year_no = document.getElementById('year').value;
  const is_new = document.getElementById('is_new').value;
  const channel = document.getElementById('channel').value;

  if (!segment) {
    console.error('No segment found on the clicked element.');
    return;
  }

  // Construct the URL with the segment
  let url = `reportchart.php?year_no=${year_no}&segment=${segment}&Sales=${Sales}&is_new=${is_new}&channel=${channel}`;

  // Fetch the data from the server
  fetch(url)
  .then(response => {
    if (!response.ok) {
        return response.text().then(text => { throw new Error(`Network response was not ok: ${text}`); });
    }
    return response.json();
})
    .then(data => {
      console.log('Fetched Data:', data); // Log the fetched data to check its structure
      updateReport(data); // Pass the correct data structure to the update function
  })
    .catch(error => console.error('Error fetching data:', error));
}
document.addEventListener('DOMContentLoaded', function() {
  const defaultElement = document.querySelector('a[data-segment-no="999"]');
  if (defaultElement) {
    fetchProduct(defaultElement);
  } 
});
let chart;

function updateReport(data) {
  const target_revenue = data.graphData.map(item => item.accumulated_target);
  const saleorderAccu = data.graphData.map(item => parseFloat(item.accumulated_so).toFixed(0));
  const dateAP = data.graphData.map(item => item.format_date);

  // Check if the chart is initialized
  if (!chart) {
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
  }
}
