// script.js
function fetchYear() {
    const year_no = document.getElementById('year').value; // Get year input
    const month_no = document.getElementById('month').value; // Get month input
    const level = document.getElementById('fetch-level').value; // Get user level
    const is_new = document.getElementById('is_new').value; // Get is_new input
    const Sales = document.getElementById('Sales').value; // Get sales input
    const channel = document.getElementById('channel').value; // Get channel input

    // Construct the URL for fetching the dashboard data
    const url = `../fetch-dashboard?year_no=${year_no}&month_no=${month_no}&channel=${channel}&Sales=${Sales}&is_new=${is_new}`;

    console.log(`Level: ${level}, Channel: ${channel}, Month: ${month_no}, Year: ${year_no}, Sales: ${Sales}, is_new: ${is_new}`);

    // Fetch dashboard data from the server
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.statusText}`); // Provide more info on error
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            console.log('Data:', data); // Log the data received from the server
            updateTable(data); // Function to update the displayed table with new data
            updateChart(data.segmentData); // Function to update a chart with the segment data
        })
        .catch(error => console.error('Error fetching data:', error)); // Log any errors that occur
}
document.addEventListener('DOMContentLoaded', fetchYear);
// Call fetchYear whenever the relevant input changes
document.getElementById('year').addEventListener('change', fetchYear);
document.getElementById('month').addEventListener('change', fetchYear);
document.getElementById('channel').addEventListener('change', fetchYear);
document.getElementById('Sales').addEventListener('change', fetchYear);
document.getElementById('is_new').addEventListener('change', fetchYear);
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
document.addEventListener('DOMContentLoaded', (event) => {
    const level = parseInt(document.getElementById('fetch-level').value);
    if (level > 1) {
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
                option.textContent = item.fname_e || item.nick_name || item.staff_id; 
                selectElement.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
      }
});
