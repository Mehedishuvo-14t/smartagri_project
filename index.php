<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Agriculture</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
      
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #eafaf1, #ffffff);
            color: #333;
        }

        a {
            text-decoration: none;
        }

       
        header {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: white;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        header h1 {
            margin: 0;
            font-size: 36px;
            letter-spacing: 1px;
        }

      
        nav {
            background-color: #2ecc71;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            padding: 12px 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        nav a {
            color: white;
            padding: 12px 25px;
            margin: 5px 8px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        nav a:hover {
            background-color: #27ae60;
            transform: translateY(-3px);
        }

       
        .welcome {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            animation: fadeIn 1s ease;
        }

        .welcome h2 {
            font-size: 32px;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .welcome p {
            font-size: 18px;
            color: #555;
        }

        main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 30px 40px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0,0,0,0.2);
        }

        .card h2 {
            color: #27ae60;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .card p {
            color: #555;
            font-size: 16px;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 25px;
            margin-top: 50px;
            font-size: 16px;
            box-shadow: 0 -3px 8px rgba(0,0,0,0.1);
        }

        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header>
    <h1>🌱 Smart Agriculture</h1>
    <nav>
        <nav>
    <a href="advisory.php">Crop Advisory</a>
    <a href="pesticide.php">Pesticide Advisory</a> 
    <a href="weather.php">Weather Alerts</a>
    <a href="marketplace.php">Marketplace</a>
    <a href="add_crop.php">Add Crop</a>
</nav>

    </nav>
</header>

<main>
    <div class="welcome">
        <h2>Welcome to SmartAgriculture.com</h2>
        <p>Helping farmers with AI-based advisory, real-time weather alerts, and a trusted crop marketplace.</p>
    </div>

    <div class="card">
        <h2>🌾 AI Crop Advisory</h2>
        <p>Get personalized crop advice powered by AI to improve yield and reduce risks.</p>
    </div>

    <div class="card">
        <h2>🌤️ Weather Alerts</h2>
        <p>Stay updated with hyper-local, real-time weather to protect your crops and plan ahead.</p>
    </div>

    <div class="card">
        <h2>🛒 Marketplace</h2>
        <p>Buy and sell crops directly with other farmers in a transparent and secure platform.</p>
    </div>
</main>

<footer>
    Smart Agriculture
</footer>

</body>
</html>
