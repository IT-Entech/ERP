fetch('../header.php')
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        const { name, staff, level, role } = data;

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
            document.getElementById('name-display').textContent = name;
            document.getElementById('name-display1').textContent = name;
            //console.log(`Name: ${name}, Staff: ${staff}, Level: ${level}, Role: ${role}`);
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });