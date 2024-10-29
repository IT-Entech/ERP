document.getElementById("register").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Fetch the form values
    const id_card = document.getElementById("yourID").value;
    const name = document.getElementById("yourName").value;
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    // Log the values for debugging purposes
    console.log("ID-Card: " + id_card);
    console.log("Name: " + name);
    console.log("Username: " + username);
    console.log("Password: " + password);

    // Prepare the AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "regis.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Handle the response
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) { // Request is complete
            if (xhr.status === 200) { // Successful response
                const response = JSON.parse(xhr.responseText);

                if (response.status === 'success') {
                    alert(response.message);
                    // Redirect to the login page
                    window.location.href = "pages-login.html";
                } else {
                    alert("Error: " + response.message);
                }
            } else {
                console.error("Error: " + xhr.status + " - " + xhr.statusText);
                alert("An error occurred. Please try again.");
            }
        }
    };

    // Send the data to the server
    xhr.send("ID-Card=" + encodeURIComponent(id_card) + 
             "&name=" + encodeURIComponent(name) + 
             "&username=" + encodeURIComponent(username) + 
             "&password=" + encodeURIComponent(password));
});
