document.getElementById("reset-password").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Fetch the form values
    const username = document.getElementById("username").value;
    const newPassword = document.getElementById("new-password").value;
    const confirmPassword = document.getElementById("confirm-password").value;

    // Log the values for debugging purposes
    console.log("Username: " + username);
    console.log("NewPassword: " + newPassword);
    console.log("ConfirmPassword: " + confirmPassword);

    // Basic validation to check if passwords match
    if (newPassword !== confirmPassword) {
    alert("Passwords do not match. Please try again.");
    return;
    }
    // Prepare the AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "reset-password.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Handle the response
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) { // Request is complete
            if (xhr.status === 200) { // Successful response
                const response = JSON.parse(xhr.responseText);

                // Handle the response based on status
                if (response.status === 'success') {
                    alert(response.message); // Show success message
                    window.location.href = "./pages-login.html";
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
             "&newPassword=" + encodeURIComponent(newPassword) +
            "&confirmPassword=" + encodeURIComponent(confirmPassword));
});
