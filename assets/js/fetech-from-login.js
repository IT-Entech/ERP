function toggleMaintenanceNav(isVisible) {
    var maintenanceNav = document.getElementById('maintanance-nav');
    var permissionNav = document.getElementById('permission-nav');
    if (isVisible) {
      maintenanceNav.classList.remove('d-none'); // Show the item
      permissionNav.classList.remove('d-none');
    } else {
      maintenanceNav.classList.add('d-none');    // Hide the item
      permissionNav.classList.add('d-none');
    }
  }

    function getSessionData() {
        fetch('header.php')
          .then(response => response.json()) // Parse the JSON from the response
          .then(data => {
            //console.log('Session Data:', data);
      
            const { name, staff, level, role, position } = data;
            //console.log(`Name: ${name}, Staff: ${staff}, Level: ${level}, Role: ${role}`);
            if (staff == 0 || level == 0) {
                alert("Can not enter this site");
                window.location.href = "../pages-login.html"; // Redirect to login
            }
            // Conditionally show Maintenance and Permission nav items
            if (level == 2 || level == 3) {
              toggleMaintenanceNav(true); 
            }
             // Update hidden fields and display the user name
             //document.getElementById('fetch-level').value = level;
             //document.getElementById('fetch-staff').value = staff;
             document.getElementById('name-display').textContent = name;
             document.getElementById('name-display1').textContent = name;
             document.getElementById('position-name').textContent = position;
          })
          .catch(error => {
            console.error('Error fetching session data:', error);
          });
      }
      // Call the function to fetch session data
getSessionData();