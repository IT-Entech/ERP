function fetchData() {
  const year_no = document.getElementById('year').value;
  const month_no = document.getElementById('month').value;
  const staff = document.getElementById('staff').value;
  let url;

  url = `api.php?year_no=${year_no}&month_no=${month_no}&staff=${staff}`;
 

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
    })
    .catch(error => console.error('Error fetching data:', error));
    }

function updateTable(data) {

  let totalap = 0;
  let totalapNoqt = 0;
  let totalSumunknown = 0;
  let totalvalueunknown = 0;
  let totalSumpotential = 0;
  let totalvaluepotential = 0;
  let totalvaluepropect = 0;
  let totalSumpropect = 0;
  let totalvaluepipeline = 0;
  let totalSumpipeline= 0;
  let totalSum = 0;
  let totalSumso = 0;

  data.revenueData.forEach(revenue => {
  totalSum += parseFloat(revenue.so_amount)|| 0;
  totalSumso += parseFloat(revenue.so_no)|| 0;
  });
        
  data.apData.forEach(ap => {
  totalap += parseFloat(ap.appoint_no)|| 0;
  totalapNoqt += parseFloat(ap.specific_appoint_no)|| 0;
  });

  data.qtData.forEach(qt => {
    totalvalueunknown += parseFloat(qt.Unknown)|| 0;
    totalSumunknown += parseFloat(qt.Unknown_amount)|| 0;
    totalvaluepotential += parseFloat(qt.potential)|| 0;
    totalSumpotential += parseFloat(qt.potential_amount)|| 0;
    totalvaluepropect += parseFloat(qt.prospect)|| 0;
    totalSumpropect += parseFloat(qt.prospect_amount)|| 0;
    totalvaluepipeline += parseFloat(qt.pipeline)|| 0;
    totalSumpipeline += parseFloat(qt.pipeline_amount)|| 0;
    });

        const countElementap = document.getElementById('appoint');
        countElementap.textContent = totalap.toLocaleString('en-US', {
        }); 

        const countElementapNoqt = document.getElementById('apNoqt');
        countElementapNoqt.textContent = totalapNoqt.toLocaleString('en-US', {
        }); 

        const UnknownElement = document.getElementById('qt_value');
        UnknownElement.textContent = totalSumunknown.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const UnknownElement1 = document.getElementById('qt_number');
        UnknownElement1.textContent = totalvalueunknown.toLocaleString('en-US', {
        }); 

        const potentialElement = document.getElementById('qt_potential_value');
        potentialElement.textContent = totalSumpotential.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const potentialElement1 = document.getElementById('qt_potential_number');
        potentialElement1.textContent = totalvaluepotential.toLocaleString('en-US', {
        });  

        const propectElement = document.getElementById('qt_prospect_value');
        propectElement.textContent = totalSumpropect.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const propectElement1 = document.getElementById('qt_prospect_number');
        propectElement1.textContent = totalvaluepropect.toLocaleString('en-US', {
        });  

        const pipelineElement = document.getElementById('qt_pipeline_value');
        pipelineElement.textContent = totalSumpipeline.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const pipelineElement1 = document.getElementById('qt_pipeline_number');
        pipelineElement1.textContent = totalvaluepipeline.toLocaleString('en-US', {
        });  

        const revenueElement = document.getElementById('revenue');
        revenueElement.textContent = totalSum.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }); 

        const countElement2 = document.getElementById('so_number');
        countElement2.textContent = totalSumso.toLocaleString('en-US', {
        });  

  const tbody = document.querySelector('#tableAP tbody');
  tbody.innerHTML = '';

  data.tableData.forEach((row, index) => {
    const tr = document.createElement('tr');

    const select = document.createElement('select');
    select.id = `status-badge-${index + 1}`;
    select.name = `status-badge-${index + 1}`;
    select.style.cursor = 'pointer';
    select.className = getBadgeClass(row.status_name); // Use the function to get the correct class
  

    const options = [
      { text: 'ไม่ได้งาน', value: 2, class: 'badge bg-danger' },
      { text: 'อยู่ระหว่างการติดตาม', value: 3, class: 'badge bg-warning' },
      { text: 'ไม่เสนอราคา', value: 4, class: 'badge bg-secondary' },
      { text: 'ได้งาน', value: 1, class: 'badge bg-success' }
    ];
    options.unshift({ text: row.status_name, value: row.status_code });

  options.forEach(optionData => {
    const options = document.createElement('option');
    options.value = optionData.value;
    options.textContent = optionData.text;
    options.className = optionData.class; 
    if (optionData.text === row.status_name) {
      options.selected = true;
      select.className = optionData.class;
    }
    select.appendChild(options);
  });
  
    const select1 = document.createElement('select');
    select1.id = `cs-badge-${index + 1}`;
    select1.name = `cs-badge-${index + 1}`;
    select1.style.cursor = 'pointer';
    select1.className = getBadgeClass(row.status1); // Use the function to get the correct class
 
    
    const optionss = [
      { text: 'PROSPEC', value: '04', class: 'badge bg-primary' },
      { text: 'POTENTIAL', value: '05', class: 'badge bg-info' },
      { text: 'PIPELINE', value: '06', class: 'badge bg-success'}
    ];

    // Add the current status as an option

    optionss.unshift({ text: row.prospect_name, value: row.prospect_code });

    // Populate the <select> element with options



    optionss.forEach(optionData => {
      const optionss = document.createElement('option');
      optionss.value = optionData.value;
      optionss.textContent = optionData.text;
      optionss.className = optionData.class; 
      if (optionData.text === row.prospect_name) {
        optionss.selected = true;
        select1.className = optionData.class;
      }
      select1.appendChild(optionss);
    });


    tr.innerHTML = `
      <td>${row.appoint_date ? row.appoint_date : ''}</td>
      <td>${row.customer_name}</td>
      <td><input type="text" class="form-control" id="qt_no${index + 1}"name="qt_no${index + 1}"value="${row.qt_no}" readonly></td>
       <td>${row.so_amount}</td>
      <td>${select1.outerHTML}</td>
      <td><input type="text" class="form-control" id="remark${index + 1}"name="remark${index + 1}"value="${row.remark ? row.remark : ''}"></td>
      <td>${select.outerHTML}</td>
      <td><input type="text" class="form-control" id="reason${index + 1}"name="reason${index + 1}"value="${row.reasoning ? row.reasoning : ''}"></td>
    `;

    tbody.appendChild(tr);
  });
}

function getBadgeClass(status) {
  switch (status) {
    case 2:
      return 'badge bg-danger';
    case 3:
      return 'badge bg-warning';
    case 4:
      return 'badge bg-secondary';
    case 1:
      return 'badge bg-success';
    default:
      return 'badge bg-warning'; // Default class if status doesn't match
  }
}

function getBadgeClass(status1) {
  switch (status1) {
    case 'PROSPEC':
      return 'badge bg-primary';
    case 'POTENTIAL':
      return 'badge bg-info';
    case 'PIPELINE':
      return 'badge bg-success';
    default:
      return 'badge bg-warning'; // Default class if status doesn't match
  }
}



document.addEventListener('DOMContentLoaded', fetchData);

/*document.addEventListener('DOMContentLoaded', (event) => {
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
});*/
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

function confirmUpdate() {
  return confirm("Are you sure you want to update the records?");
}
