document.getElementById("login").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Fetch the form values
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    // Log the values for debugging purposes
    console.log("Username: " + username);
    console.log("Password: " + password);

    // Prepare the AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "get_login.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Handle the response
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) { // Request is complete
            if (xhr.status === 200) { // Successful response
                const response = JSON.parse(xhr.responseText);

                // Handle the response based on status
                if (response.status === 'success') {
                    alert(response.message); // Show success message
                    
                    // Redirect based on user level
                    if (response.level === 1) {
                        window.location.href = "./";
                    } else if (response.level > 1) {
                        window.location.href = "./";
                    } else {
                        // This case shouldn't normally be hit
                        alert("Unexpected user level. Please contact support.");
                    }
                } else {
                    // Show error message from server
                    alert("Error: " + response.message);
                }
            } else {
                console.error("Error: " + xhr.status + " - " + xhr.statusText);
                alert("An error occurred. Please try again.");
            }
        }
    };

    // Send the data to the server
    xhr.send("username=" + encodeURIComponent(username) + 
             "&password=" + encodeURIComponent(password));
});
