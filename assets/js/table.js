function fetchData() {
  const year_no = document.getElementById('year').value;
  const month_no = document.getElementById('month').value;
  const channel = document.getElementById('channel').value;
  const Sales = document.getElementById('Sales').value;
  let url;

  url = `api.php?year_no=${year_no}&month_no=${month_no}&channel=${channel}&Sales=${Sales}`;
 

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
  const tbody = document.querySelector('#tableAP tbody');
  tbody.innerHTML = '';

  data.forEach((row, index) => {
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

   

    const reasonInput = document.createElement('input');
    reasonInput.type = 'text';
    reasonInput.className = 'form-control';
    reasonInput.id = `reason${index + 1}`;
    reasonInput.name = `reason${index + 1}`;
    reasonInput.value = row.reasoning;
    reasonInput.disabled = row.status_code != 1 && row.status_code != 2;//



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
      <td><a href= "forms-appoint.html?ap=${row.appoint_no}" id= "${row.date}" value ="${row.date}" >${row.date}</a></td>
      <td>${row.name}</td>
      <td>${row.qt_no}</td>
       <td>${row.so_amount}</td>
      <td>${select1.outerHTML}</td>
      <td><input type="text" class="form-control" id="remark${row.appoint_no}${index + 1}"name="${row.appoint_no}"value="${row.remark}"</td>
      <td>${select.outerHTML}</td>
      <td>${reasonInput.outerHTML}</td>
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
