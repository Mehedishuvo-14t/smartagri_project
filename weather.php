<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

$apiKey = "cdcdc07bd59a4115bba190416251409"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Weather - Smart Agriculture</title>
<link rel="stylesheet" href="styles.css">
<style>
body { font-family: Arial; background-color: #f4f6f8; margin: 0; padding: 0; }
header { background-color: #27ae60; color: white; padding: 20px; text-align: center; }
header h1 { margin: 0; font-size: 28px; }
nav { background-color: #2ecc71; display: flex; justify-content: center; flex-wrap: wrap; }
nav a { color: white; text-decoration: none; padding: 15px 25px; margin: 5px; display: inline-block; transition: 0.3s; }
nav a:hover { background-color: #27ae60; }
.weather-container { max-width: 500px; margin: 40px auto; background-color: white; border-radius: 10px; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; }
.weather-container h2 { margin-bottom: 20px; color: #27ae60; }
.weather-container p { font-size: 18px; color: #555; margin: 10px 0; }
.weather-form { margin-top: 20px; }
.weather-form input[type="text"] { padding: 8px; width: 70%; border: 1px solid #ccc; border-radius: 5px; }
.weather-form button { padding: 8px 15px; border: none; background-color: #27ae60; color: white; border-radius: 5px; cursor: pointer; }
.weather-form button:hover { background-color: #219150; }
.error { color: #e74c3c; font-weight: bold; }
footer { background-color:#2c3e50; color:white; text-align:center; padding:15px; margin-top:40px; }
</style>
</head>
<body>

<header>
    <h1>🌤️ Local Weather</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="add_crop.php">Add Crop</a>
    <a href="marketplace.php">Marketplace</a>
    <a href="weather.php">Weather</a>
    <a href="advisory.php">Advisory</a>
</nav>

<div class="weather-container" id="weather-container">
    <p>Fetching your local weather...</p>
</div>


<div class="weather-container">
    <form class="weather-form" id="cityForm">
        <input type="text" name="city" id="cityInput" placeholder="Enter city or upazila">
        <button type="submit">Check Weather</button>
    </form>
</div>

<footer>
    Smart Agriculture
</footer>

<script>

function renderWeather(data) {
    if (data.error) {
        document.getElementById('weather-container').innerHTML =
            "<p class='error'>Error: " + data.error.message + "</p>";
    } else {
        document.getElementById('weather-container').innerHTML = `
            <h2>Weather in ${data.location.name}, ${data.location.country}</h2>
            <p>Temperature: ${data.current.temp_c} °C</p>
            <p>Condition: ${data.current.condition.text}</p>
            <p>Humidity: ${data.current.humidity}%</p>
            <p>Wind Speed: ${data.current.wind_kph} km/h</p>
        `;
    }
}


function fetchWeatherByCoords(lat, lon) {
    fetch("http://api.weatherapi.com/v1/current.json?key=<?php echo $apiKey; ?>&q=" + lat + "," + lon + "&aqi=no")
    .then(response => response.json())
    .then(data => renderWeather(data))
    .catch(() => {
        document.getElementById('weather-container').innerHTML =
            "<p class='error'>Unable to fetch weather data.</p>";
    });
}


function fetchWeatherByCity(city) {
    fetch("http://api.weatherapi.com/v1/current.json?key=<?php echo $apiKey; ?>&q=" + city + "&aqi=no")
    .then(response => response.json())
    .then(data => renderWeather(data))
    .catch(() => {
        document.getElementById('weather-container').innerHTML =
            "<p class='error'>Unable to fetch weather data.</p>";
    });
}


if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        fetchWeatherByCoords(position.coords.latitude, position.coords.longitude);
    }, function() {
        document.getElementById('weather-container').innerHTML =
            "<p class='error'>Geolocation not enabled. Please enter your city below.</p>";
    });
} else {
    document.getElementById('weather-container').innerHTML =
        "<p class='error'>Geolocation not supported by your browser. Please enter your city below.</p>";
}


document.getElementById('cityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var city = document.getElementById('cityInput').value.trim();
    if (city !== "") {
        fetchWeatherByCity(city);
    }
});
</script>

</body>
</html>
