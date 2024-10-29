function fetchData() {

    fetch('Query_permission.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log('Data:', data); // Log the data to check the response
      Table(data);
    })
    .catch(error => console.error('Error fetching data:', error));
    }

function Table(data) {
  const tbody = document.querySelector('#tablepm tbody');
  tbody.innerHTML = '';

  data.permission.forEach((row, index) => {
    const tr = document.createElement('tr');

    const select = document.createElement('select');
    select.id = `level${index + 1}`;
    select.name = `level${index + 1}`;
    select.style.cursor = 'pointer';
    //select.className = getBadgeClass(row.status_name); // Use the function to get the correct class
  

    const options = [
      { text: 'User', value: 1},
      { text: 'Admin', value: 2},
      { text: 'Super Admin', value: 3}
    ];
    options.unshift({ 
        text: row.level === 1 ? 'User' : row.level === 2 ? 'Admin' : row.level === 3 ? 'Super Admin' : 'Other', 
        value: row.level 
      });

  options.forEach(optionData => {
    const options = document.createElement('option');
    options.value = optionData.value;
    options.textContent = optionData.text;
    select.appendChild(options);
  });
  
    const select1 = document.createElement('select');
    select1.id = `Role${index + 1}`;
    select1.name = `Role${index + 1}`;
    select1.style.cursor = 'pointer';
    //select1.className = getBadgeClass(row.status1); // Use the function to get the correct class
 
    
   let optionss = [
      { text: 'Unknown', value: 'Unknown'},
      { text: 'MK Online', value: 'MK Online' },
      { text: 'MK Offline', value: 'MK Offline' },
      { text: 'MK', value: 'MK' },
      { text: 'Super Admin', value: 'SUPER ADMIN'},
      { text: 'OPL', value: 'OPL'}
    ];

    // Add the current status as an option

    const newOption = {
        text: row.Role === 'MK' ? 'MK' :
              row.Role === 'MK Online' ? 'MK Online' :
              row.Role === 'SUPER ADMIN' ? 'SUPER ADMIN' :
              row.Role === 'MK Offline' ? 'MK Offline' : 
              row.Role === 'OPL' ? 'OPL' : 'Unknown',
        value: row.Role
    };
    
    // Remove existing option with the same value as row.Role
    optionss = optionss.filter(option => option.value !== newOption.value);
    
    // Add the new option at the beginning
    optionss.unshift(newOption);

    // Populate the <select> element with options

    optionss.forEach(optionData => {
      const optionss = document.createElement('option');
      optionss.value = optionData.value;
      optionss.textContent = optionData.text;
      select1.appendChild(optionss);
    });

    tr.innerHTML = `
    <th scope='row'><input type="text" class="form-control" id="id${index + 1}" 
         name="id${index + 1}" 
         value="${row.id}" 
         readonly></th>
     <td>${row.Name}</td>
    <td>${select.outerHTML}</td>
    <td>${select1.outerHTML}</td>
     <td style="text-align: center;">
  <input type="hidden" name="active${index + 1}" value="N">
  <input class="form-check-input" 
         type="checkbox" 
         id="active${index + 1}" 
         name="active${index + 1}" 
         value="Y" 
         ${row.active === 'Y' ? 'checked' : ''}>
</td>

    `;

    tbody.appendChild(tr);
  });
}
document.addEventListener('DOMContentLoaded', fetchData);

