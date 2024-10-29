function fetchData() {
  const year_no = document.getElementById('year').value;
  const month_no = document.getElementById('month').value;
  const channel = document.getElementById('channel').value;

  let url;

  url = `fetch_CS.php?year_no=${year_no}&month_no=${month_no}&channel=${channel}`;

  fetch(url)
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    console.log('Data:', data); // Log the data to check the response
    EditCS(data);
  })
  .catch(error => console.error('Error fetching data:', error));
}

function EditCS(data) {
  const tableBody = document.getElementById('costsheet');
  if (!tableBody) {
    console.error('Error: Table body element not found');
    return;
  }
  tableBody.innerHTML = ''; // Clear existing data

  data.forEach(item => {
    const row = document.createElement('tr');
    
    const idCell = document.createElement('th');
    idCell.scope = 'row';
    const idLink = document.createElement('a');
    idLink.href = `#${item.qt_no}`;
    idLink.textContent = `#${item.qt_no}`;
    idCell.appendChild(idLink);
    row.appendChild(idCell);

    const nameCell = document.createElement('td');
    nameCell.textContent = item.customer_name;
    row.appendChild(nameCell);

    const descCell = document.createElement('td');
    descCell.textContent = item.province_code; // Adjusted to show province_code
    row.appendChild(descCell);

    const amountCell = document.createElement('td');
    amountCell.textContent = `$${item.so_amount}`;
    row.appendChild(amountCell);

    const statusCell = document.createElement('td');
    const statusSpan = document.createElement('span');
    statusSpan.className = 'badge bg-success'; // Assuming all statuses are 'Approved'
    statusSpan.textContent = 'Approved'; // Change based on your actual status logic
    statusCell.appendChild(statusSpan);
    row.appendChild(statusCell);

    tableBody.appendChild(row);
  });
}

document.addEventListener('DOMContentLoaded', fetchData);
