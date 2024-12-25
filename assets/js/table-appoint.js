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
      <td><input type="text" class="form-control" id="appoint_no${index + 1}"name="appoint_no${index + 1}"value="${row.appoint_no}" readonly></td>
    <td>
  <select id="status-${row.appoint_no}" name="status${index+1}" class="form-select text-center ${row.is_status == 0 ? 'bg-secondary text-white' : row.is_status == 2 ? 'bg-danger text-white' : row.is_status == 3 ? 'bg-warning text-muted' : row.is_status == 4 ? 'bg-danger text-white' : ''}"  onchange="handleSelectChange('${row.appoint_no}')">
    <option value="${row.is_status}">
      ${row.is_status == 0 ? 'N/A' : row.is_status == 2 ? 'Rejected' : row.is_status == 3 ? 'Pending' : row.is_status == 4 ? 'ไม่เสนอราคา' : row.is_status}
    </option>
<option value="${row.is_status == 0 ? 3 : row.is_status == 4 ? 'N/A' : '0'}">
  ${row.is_status == 0 ? 'Pending' : row.is_status == 4 ? 'ไม่เสนอราคา' : 'N/A'}
</option>

  </select>
</td> 
      <td>
  <input 
    type="text" 
    name="remark${index+1}" 
    class="form-control" 
    value="${row.remark ? row.remark : ''}" 
    id="remark-${row.appoint_no}"
    ${row.is_status == 0 ? 'disabled' : ''}
  >
</td>
      <td>${row.update_time}</td>
    `;
    tbody.appendChild(tr);
  });
}
function handleSelectChange(appointNo) {                              
  const selectElement = document.getElementById(`status-${appointNo}`);
  const inputElement = document.getElementById(`remark-${appointNo}`);

  // Get the selected value from the select element
  const selectedValue = selectElement.value;

  // If the selected value is 0, disable the input field
  if (selectedValue == 0) {
    inputElement.disabled = true;
  } else {
    inputElement.disabled = false;
  }
   // Remove any existing color classes
   selectElement.classList.remove('bg-secondary', 'bg-warning', 'text-white', 'text-muted');

   // Add the appropriate class based on the selected value
   if (selectedValue == 0) {
     selectElement.classList.add('bg-secondary', 'text-white'); // Grey for N/A
   } else if (selectedValue == 3) {
     selectElement.classList.add('bg-warning', 'text-muted');   // Yellow for Pending
   } else if (selectedValue == 4) {
    selectElement.classList.add('bg-danger', 'text-muted');   // Yellow for Pending
  }
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
