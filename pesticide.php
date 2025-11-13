<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';
$apiKey = "cdcdc07bd59a4115bba190416251409";
// If you don't have config.php holding weather api key, you can set here:
// $apiKey = "your_weatherapi_key_here";
// Otherwise ensure config.php sets $apiKey
?>
<!doctype html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<!--<title>Weather → Crop Spray Advisor (SmartAgri)</title>-->

<!-- ======= NAVBAR ======= -->
<div class="navbar">
    <div class="nav-container">
        <div class="logo">SmartAgri</div>
        <div class="menu">
            <a href="index.php">Home</a>
            <a href="weather.php">Weather</a>
            <a href="advisory.php">Advisory</a>
            <a href="add_crop.php">Add Crop</a>
        </div>
    </div>
</div>

<style>
/* NAVBAR WRAPPER */
.navbar{
    background:#2e7d32;
    padding:12px 0;
    box-shadow:0 3px 8px rgba(0,0,0,0.10);
}

/* GRID BASED NAV */
.nav-container{
    max-width:1100px;
    margin:auto;
    padding:0 15px;

    display:grid;
    grid-template-columns:200px 1fr;
    align-items:center;
}

/* LOGO */
.logo{
    color:#fff;
    font-size:24px;
    font-weight:700;
    letter-spacing:1px;
}

/* MENU LINKS */
.menu{
    text-align:right;
}

.menu a{
    color:#fff;
    text-decoration:none;   /* underline remove */
    margin-left:25px;
    font-size:17px;
    font-weight:500;
    padding:8px 4px;
    border-radius:6px;
    transition:0.25s;
}

/* HOVER EFFECT */
.menu a:hover{
    background:#ffffff22;
    padding:8px 10px;
}
</style>



<!-- ===== END NAVBAR ===== -->




<style>
    body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;color:#222}
    header{background:#2e7d32;color:#fff;padding:18px 12px;text-align:center}
    .wrap{max-width:900px;margin:24px auto;padding:16px}
    .card{background:#fff;border-radius:10px;padding:18px;box-shadow:0 6px 18px rgba(0,0,0,.08);margin-bottom:16px}
    label{display:block;font-weight:700;margin-bottom:6px}
    select,input[type="text"]{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px}
    .controls{display:grid;grid-template-columns:1fr 140px;gap:12px}
    button{background:#2e7d32;color:#fff;border:none;padding:10px;border-radius:6px;cursor:pointer}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px}
    .box{background:#fafafa;padding:12px;border-radius:8px;border:1px solid #eee}
    .ok{color:green;font-weight:700}
    .bad{color:#d9534f;font-weight:700}
    .neutral{color:#333}
    pre{white-space:pre-wrap;background:#e8f5e9;padding:12px;border-radius:6px}
    @media(max-width:600px){.controls{grid-template-columns:1fr} .info-grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<header>
    <h1>ফসল স্প্রে পরামর্শ (আর্থ-লোকাল)</h1>
</header>

<div class="wrap">
    <div class="card">
        <label>ফসল নির্বাচন করুন</label>
        <select id="cropSelect">
            <!-- ২৫টি ডিফল্ট ফসল -->
            <option value="ধান">ধান</option>
            <option value="গম">গম</option>
            <option value="ভুট্টা">ভুট্টা</option>
            <option value="পেঁয়াজ">পেঁয়াজ</option>
            <option value="রসুন">রসুন</option>
            <option value="আলু">আলু</option>
            <option value="টমেটো">টমেটো</option>
            <option value="বেগুন">বেগুন</option>
            <option value="মরিচ">মরিচ</option>
            <option value="মসুর ডাল">মসুর ডাল</option>
            <option value="ছোলা">ছোলা</option>
            <option value="সয়াবিন">সয়াবিন</option>
            <option value="সরিষা">সরিষা</option>
            <option value="পাট">পাট</option>
            <option value="তরমুজ">তরমুজ</option>
            <option value="কুমড়া">কুমড়া</option>
            <option value="লাউ">লাউ</option>
            <option value="শসা">শসা</option>
            <option value="পেঁপে">পেঁপে</option>
            <option value="আম">আম</option>
            <option value="কলা">কলা</option>
            <option value="লিচু">লিচু</option>
            <option value="চা">চা</option>
            <option value="টিল">টিল</option>
            <option value="আখ">আখ</option>
        </select>

        <div style="margin-top:12px" class="controls">
            <input type="text" id="cityInput" placeholder="নাম / উপজেলা লিখুন (ভিত্তিমূলক)">
            <button id="checkBtn">Check Now</button>
        </div>

        <p style="margin-top:10px;color:#555">অথবা ব্রাউজার লোকেশন অন করলে স্বয়ংক্রিয়ভাবে নিজের লোকাল ওয়েদার দেখাবে।</p>
    </div>

    <div class="card" id="resultCard" style="display:none">
        <h3 id="locTitle">—</h3>

        <div class="info-grid">
            <div class="box">
                <b>বর্তমান আবহাওয়া</b>
                <p id="curTemp">তাপমাত্রা: —</p>
                <p id="curHum">আর্দ্রতা: —</p>
                <p id="curWind">বাতাস: —</p>
                <p id="curCond">স্থিতি: —</p>
            </div>

            <div class="box">
                <b>ফসলের আদর্শ মান (Optimum)</b>
                <p id="optTemp">তাপমাত্রা: —</p>
                <p id="optHum">আর্দ্রতা: —</p>
                <p id="optWind">বাতাস: —</p>
            </div>
        </div>

        <div style="margin-top:12px" class="box">
            <b>বিশ্লেষণ ও সুপারিশ</b>
            <div id="analysis" style="margin-top:8px;color:#222"></div>
        </div>

        <div style="margin-top:12px" class="box">
            <b>স্প্রে করার সিদ্ধান্ত</b>
            <div id="finalAdvice" style="margin-top:8px;font-weight:700"></div>
        </div>
    </div>
</div>

<script>
/* =========================
   Optimum data (25 crops)
   ========================= */
const cropOpt = {
    "ধান":      {temp:[20,35], humidity:[60,85], wind:[0,15]},
    "গম":       {temp:[10,25], humidity:[50,60], wind:[0,20]},
    "ভুট্টা":    {temp:[18,27], humidity:[50,70], wind:[0,18]},
    "পেঁয়াজ":    {temp:[15,25], humidity:[50,60], wind:[0,12]},
    "রসুন":     {temp:[10,20], humidity:[60,70], wind:[0,10]},
    "আলু":      {temp:[15,20], humidity:[65,80], wind:[0,10]},
    "টমেটো":    {temp:[18,25], humidity:[60,70], wind:[0,12]},
    "বেগুন":    {temp:[20,30], humidity:[60,80], wind:[0,12]},
    "মরিচ":     {temp:[20,30], humidity:[60,70], wind:[0,15]},
    "মসুর ডাল":  {temp:[15,25], humidity:[40,60], wind:[0,20]},
    "ছোলা":     {temp:[15,30], humidity:[40,60], wind:[0,18]},
    "সয়াবিন":   {temp:[20,30], humidity:[60,80], wind:[0,15]},
    "সরিষা":    {temp:[10,25], humidity:[50,70], wind:[0,15]},
    "পাট":      {temp:[22,38], humidity:[70,90], wind:[0,10]},
    "তরমুজ":    {temp:[22,35], humidity:[60,70], wind:[0,15]},
    "কুমড়া":    {temp:[20,30], humidity:[60,80], wind:[0,14]},
    "লাউ":      {temp:[20,30], humidity:[60,80], wind:[0,14]},
    "শসা":      {temp:[20,30], humidity:[60,80], wind:[0,12]},
    "পেঁপে":     {temp:[22,32], humidity:[60,80], wind:[0,10]},
    "আম":       {temp:[25,35], humidity:[50,70], wind:[0,20]},
    "কলা":      {temp:[25,35], humidity:[70,90], wind:[0,10]},
    "লিচু":     {temp:[25,32], humidity:[60,80], wind:[0,12]},
    "চা":       {temp:[20,30], humidity:[70,90], wind:[0,10]},
    "টিল":      {temp:[20,35], humidity:[60,75], wind:[0,12]},
    "আখ":      {temp:[20,30], humidity:[60,80], wind:[0,15]}
};

/* =========================
   Utility helper functions
   ========================= */

function formatC(x){ return (x===null||x===undefined)?'—': x + " °C"; }
function formatPct(x){ return (x===null||x===undefined)?'—': x + " %"; }
function formatKph(x){ return (x===null||x===undefined)?'—': x + " km/h"; }

function analyzeAndAdvise(cropName, temp, hum, wind) {
    const opt = cropOpt[cropName];
    if(!opt) return {analysis:"ফসলের ডেটা পাওয়া যায় নাই।", advice:"N/A"};

    const tempOK = temp >= opt.temp[0] && temp <= opt.temp[1];
    const humOK = hum >= opt.humidity[0] && hum <= opt.humidity[1];
    const windOK = wind <= opt.wind[1];

    // Build analysis text (Bangla)
    let analysis = "";
    analysis += `ফসল: ${cropName}\n\n`;
    analysis += `বর্তমান: তাপ ${temp}°C, আর্দ্রতা ${hum}%, বাতাস ${wind} km/h\n\n`;
    analysis += `Optimum: তাপ ${opt.temp[0]}–${opt.temp[1]}°C, আর্দ্রতা ${opt.humidity[0]}–${opt.humidity[1]}%, বাতাস ≤ ${opt.wind[1]} km/h\n\n`;

    analysis += "অবস্থা: \n";
    analysis += tempOK ? "• তাপমাত্রা: ✔ উপযুক্ত\n" : "• তাপমাত্রা: ✖ উপযুক্ত নয়\n";
    analysis += humOK  ? "• আর্দ্রতা: ✔ উপযুক্ত\n" : "• আর্দ্রতা: ✖ উপযুক্ত নয়\n";
    analysis += windOK ? "• বাতাস: ✔ উপযুক্ত\n" : "• বাতাস: ✖ বেশি\n";

    // Determine wait time logic (primary: wind)
    let advice = "";
    if(tempOK && humOK && windOK){
        advice = "✅ স্প্রে করা নিরাপদ — এখনই স্প্রে করা যায়।";
    } else {
        // If wind too high, compute wait time: per 1 km/h excess -> 15 minutes wait
        if(!windOK){
            const excess = Math.max(0, wind - opt.wind[1]);
            const waitMin = Math.ceil(excess * 15); // 15 min per km/h excess
            advice += `⚠ বাতাস বেশি (${wind} km/h)। বাতাস ≤ ${opt.wind[1]} km/h এ নেমে গেলে স্প্রে করুন।\n`;
            advice += `অণুমানিক অপেক্ষা করুন: ~ ${waitMin} মিনিট।\n`;
        }
        // If humidity not ok
        if(!humOK){
            if(hum < opt.humidity[0]){
                advice += `⚠ আর্দ্রতা কম (বর্তমান ${hum}%) — স্প্রে করলে ছিটে শুকিয়ে যেতে পারে। ১–৩ ঘণ্টা অপেক্ষা করে পুনঃপরীক্ষা করুন।\n`;
            } else {
                advice += `⚠ আর্দ্রতা বেশি (বর্তমান ${hum}%) — ফোঁটা জমে পড়লে স্প্রে কার্যকারিতা কমতে পারে। আর্দ্রতা কমলে স্প্রে করুন।\n`;
            }
        }
        // If temp not ok
        if(!tempOK){
            advice += `⚠ তাপমাত্রা আদর্শের বাইরে — তাপমাত্রা ${opt.temp[0]}–${opt.temp[1]}°C এ থাকলে স্প্রে করা ভাল।\n`;
        }

        // If we only have wind issue, final message already includes waitMin.
        if(advice === "") advice = "পর্যবেক্ষণ করুন এবং উপযুক্ত অবস্থায় স্প্রে করুন।";
        else advice = "সাবধান: \n" + advice;
    }

    return {analysis, advice};
}

/* =========================
   Weather fetching
   ========================= */

// WeatherAPI key from PHP variable
const apiKey = "<?php echo htmlspecialchars($apiKey ?? ''); ?>";

function renderError(msg){
    document.getElementById('resultCard').style.display = 'block';
    document.getElementById('locTitle').textContent = "ত্রুটি";
    document.getElementById('analysis').textContent = msg;
    document.getElementById('finalAdvice').textContent = "";
    document.getElementById('curTemp').textContent = "তাপমাত্রা: —";
    document.getElementById('curHum').textContent = "আর্দ্রতা: —";
    document.getElementById('curWind').textContent = "বাতাস: —";
    document.getElementById('curCond').textContent = "স্থিতি: —";
    document.getElementById('optTemp').textContent = "তাপমাত্রা: —";
    document.getElementById('optHum').textContent = "আর্দ্রতা: —";
    document.getElementById('optWind').textContent = "বাতাস: —";
}

function updateUI(locationName, country, temp, hum, wind, cond, crop) {
    document.getElementById('resultCard').style.display = 'block';
    document.getElementById('locTitle').textContent = `${locationName}, ${country}`;

    document.getElementById('curTemp').textContent = `তাপমাত্রা: ${temp} °C`;
    document.getElementById('curHum').textContent = `আর্দ্রতা: ${hum}%`;
    document.getElementById('curWind').textContent = `বাতাস: ${wind} km/h`;
    document.getElementById('curCond').textContent = `স্থিতি: ${cond}`;

    const opt = cropOpt[crop];
    document.getElementById('optTemp').textContent = `তাপমাত্রা: ${opt.temp[0]}–${opt.temp[1]} °C`;
    document.getElementById('optHum').textContent = `আর্দ্রতা: ${opt.humidity[0]}–${opt.humidity[1]}%`;
    document.getElementById('optWind').textContent = `বাতাস: 0–${opt.wind[1]} km/h`;

    const res = analyzeAndAdvise(crop, temp, hum, wind);
    document.getElementById('analysis').textContent = res.analysis;
    document.getElementById('finalAdvice').textContent = res.advice;
}

function fetchWeatherByCoords(lat, lon, crop) {
    if (!apiKey) { renderError("API key সেট করা হয়নি (WeatherAPI)"); return; }
    fetch(`https://api.weatherapi.com/v1/current.json?key=${apiKey}&q=${lat},${lon}&aqi=no`)
        .then(r => r.json())
        .then(data => {
            if(data.error){ renderError(data.error.message); return; }
            const temp = Math.round(data.current.temp_c);
            const hum = Math.round(data.current.humidity);
            const wind = Math.round(data.current.wind_kph);
            updateUI(data.location.name, data.location.country, temp, hum, wind, data.current.condition.text, crop);
        })
        .catch(err => { renderError("Weather API error"); console.error(err); });
}

function fetchWeatherByCity(city, crop) {
    if (!apiKey) { renderError("API key সেট করা হয়নি (WeatherAPI)"); return; }
    fetch(`https://api.weatherapi.com/v1/current.json?key=${apiKey}&q=${encodeURIComponent(city)}&aqi=no`)
        .then(r => r.json())
        .then(data => {
            if(data.error){ renderError(data.error.message); return; }
            const temp = Math.round(data.current.temp_c);
            const hum = Math.round(data.current.humidity);
            const wind = Math.round(data.current.wind_kph);
            updateUI(data.location.name, data.location.country, temp, hum, wind, data.current.condition.text, crop);
        })
        .catch(err => { renderError("Weather API error"); console.error(err); });
}

/* =========================
   Event handlers
   ========================= */

document.getElementById('checkBtn').addEventListener('click', function(e){
    e.preventDefault();
    const crop = document.getElementById('cropSelect').value;
    const city = document.getElementById('cityInput').value.trim();
    if(city) {
        fetchWeatherByCity(city, crop);
    } else {
        // try geolocation
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(pos => {
                fetchWeatherByCoords(pos.coords.latitude, pos.coords.longitude, crop);
            }, () => {
                renderError("লোকেশন নেয়া সম্ভব হলো না — শহর লিখুন এবং Check করুন।");
            }, {timeout:10000});
        } else {
            renderError("ব্রাউজার লোকেশন সাপোর্ট করে না — শহর লিখুন।");
        }
    }
});

// Auto load on page open with selected crop & geolocation (best effort)
window.addEventListener('load', function(){
    const crop = document.getElementById('cropSelect').value;
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(pos => {
            fetchWeatherByCoords(pos.coords.latitude, pos.coords.longitude, crop);
        }, () => {
            // do nothing, wait for manual input
        }, {timeout:10000});
    }
});
</script>
</body>
</html>
