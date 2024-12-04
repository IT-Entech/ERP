
    function getSessionData() {
        fetch('header.php')
          .then(response => response.json()) // Parse the JSON from the response
          .then(data => {
            //console.log('Session Data:', data);
      
            const { name, staff, level, role, position } = data;
            //console.log(`Name: ${name}, Staff: ${staff}, Level: ${level}, Role: ${role}`);
            if (staff == 0 || level == 0) {
                alert("Cannot enter this site.");
                window.location.href = "pages-login.html";
                return; // Redirect to login
            }
            var permissionNav = document.getElementById('permission-nav');
      var maintenanceNav = document.getElementById('maintanance-nav');
      if(role == 'MK Online' || role == 'MK Offline'){
        
        permissionNav.classList.add('d-none');
        maintenanceNav.classList.add('d-none'); 
      }else{
        permissionNav.classList.remove('d-none');
        maintenanceNav.classList.remove('d-none');
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