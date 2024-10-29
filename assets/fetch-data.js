   // Function to get query parameter by name
        function getQueryParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // Get the 'ap' parameter from the URL
        const apValue = getQueryParameter('ap');

        // Now you can use the apValue for your fetch or other logic
        console.log(apValue); // For demonstration, you can remove this line

        // Example: Fetch data using the apValue
        fetch(`your-api-endpoint?ap=${apValue}`)
            .then(response => response.json())
            .then(data => {
                // Process your data here
                console.log(data);
            })
            .catch(error => console.error('Error fetching data:', error));