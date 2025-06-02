// Define the units for each unit type
const units = {
    temperature: ['celsius', 'fahrenheit', 'kelvin'],
    length: ['meter', 'foot', 'inch'],
    weight: ['kilogram', 'pound', 'ounce'],
    volume: ['liter', 'gallon', 'milliliter']
};

// Function to update the 'From' and 'To' dropdowns
function updateUnits() {
    console.log("updateUnits called at " + new Date().toLocaleString());
    const unitTypeElement = document.getElementById('unitType');
    if (!unitTypeElement) {
        console.error("Unit Type element not found with ID 'unitType'");
        return;
    }
    const unitType = unitTypeElement.value;
    console.log("Selected Unit Type:", unitType);

    const fromUnit = document.getElementById('fromUnit');
    const toUnit = document.getElementById('toUnit');
    if (!fromUnit || !toUnit) {
        console.error("Dropdown elements not found:", { fromUnit, toUnit });
        return;
    }

    fromUnit.innerHTML = '';
    toUnit.innerHTML = '';
    if (units[unitType]) {
        units[unitType].forEach((unit, index) => {
            const option = `<option value="${unit}">${unit.charAt(0).toUpperCase() + unit.slice(1)}</option>`;
            fromUnit.innerHTML += option;
            toUnit.innerHTML += option;
            if (index === 0) {
                fromUnit.value = unit;
                toUnit.value = unit;
                console.log("Default unit set to:", unit);
            }
        });
    } else {
        console.error("Invalid unit type:", unitType);
    }
}

// Function to fetch weather data from OpenWeatherMap API
async function fetchWeather() {
    const city = document.getElementById('city')?.value || '';
    const apiKey = '6415b7528f1f13f5dd2d54a1a97736ac'; // Replace with your actual API key
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;
    try {
        const response = await fetch(url);
        const data = await response.json();
        if (data.cod === 200) {
            document.getElementById('weatherResult').innerHTML = `Current temperature in ${city}: ${data.main.temp}°C`;
        } else {
            document.getElementById('weatherResult').innerHTML = `<div class="alert alert-danger">City not found!</div>`;
        }
    } catch (error) {
        document.getElementById('weatherResult').innerHTML = `<div class="alert alert-danger">Error fetching weather data!</div>`;
        console.error("Weather API error:", error);
    }
}

// Chart.js setup for visualizing conversion history
fetch('api.php')
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        console.log("Chart data received:", data);
        const ctx = document.getElementById('conversionChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.created_at),
                    datasets: [{
                        label: 'Conversion Results',
                        data: data.map(item => item.result),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: false }
                    }
                }
            });
        } else {
            console.error("Canvas element 'conversionChart' not found");
        }
    })
    .catch(error => {
        console.error("Error fetching chart data:", error);
    });

// Run updateUnits on page load
window.onload = function() {
    console.log("Page loaded at " + new Date().toLocaleString());
    updateUnits();
};
