fetch('../header.php')
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        const { name, staff, level, role } = data;
        if (level >= '2') {
          fetch('../staff_id.php')
          .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
          })
          .then(data => {
            const selectElement = document.getElementById('sales');
            data.forEach(item => {
              const option = document.createElement('option');
              option.value = item.staff_id;
              option.textContent = item.fname_e || item.nick_name || item.staff_id;
              selectElement.appendChild(option);
            });
          })
          .catch(error => console.error('Error fetching staff data:', error));
      }else if(level == '1'){
        const selectElement = document.getElementById('sales');
        const option = document.createElement('option');
              option.value = staff;
              option.textContent = name;
              selectElement.appendChild(option);
      }
        if (staff === 0 || level == 0) {
            alert("Cannot enter this site");
            window.location.href = "../pages-login.html"; // Redirect to login
        } else {
            if (level == 3) {
                document.getElementById('maintanance-nav').style.display = 'block';
                document.getElementById('permission-nav').style.display = 'block';
                document.getElementById('select-sale').style.display = 'block';
            } else if (level >= 2) {
                document.getElementById('select-sale').style.display = 'block';
            }
            // Update hidden fields and display the user name
            document.getElementById('fetch-level').value = level;
            document.getElementById('fetch-staff').value = staff;
            document.getElementById('fetch-role').value = role;
            document.getElementById('name-display').textContent = name;
            document.getElementById('name-display1').textContent = name;

         
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });
function fetchData() {
  const year_no = document.getElementById('year').value;
  const month_no = document.getElementById('month').value;
  const Sales = document.getElementById('sales').value;

  const url = `./fetch-appoint.php?year_no=${year_no}&month_no=${month_no}&Sales=${Sales}`;
  console.log('Fetching data from URL:', url); 

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

  data.tableData.forEach((row, index) => {
    if (!row || !row.appoint_no) {
      console.error(`Row ${index + 1} is invalid:`, row);
      return; // Skip this row if it's invalid
    }
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <th scope='row'>${index+1}</th>
      <td>${row.format_date ? row.format_date : ''}</td>
      <td>${row.customer_name}</td>
      <td>${row.appoint_no}</td>
    `;
    tbody.appendChild(tr);
  });
}
document.addEventListener('DOMContentLoaded', fetchData);

/*document.addEventListener('DOMContentLoaded', (event) => {
  fetch('../staff_id.php')
      .then(response => {
          if (!response.ok) {
              throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.json();
      })
      .then(data => {
          const selectElement = document.getElementById('sales');
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
