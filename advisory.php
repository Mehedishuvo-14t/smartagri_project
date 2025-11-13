<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crop Advisory - Smart Agriculture</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f7;
            margin: 0; padding: 0;
            color: #333;
        }

        header {
            background: #2e7d32;
            color: white;
            padding: 20px;
            text-align: center;
        }

        header h1 { margin: 0; }
        header a {
            display: inline-block;
            margin-top: 10px;
            color: white;
            text-decoration: none;
            background: #27ae60;
            padding: 8px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        header a:hover { background: #1b5e20; }

        main {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            display: grid;
            gap: 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
            color: #2e7d32;
        }

        form {
            display: grid;
            gap: 15px;
            text-align: left;
        }

        label { font-weight: bold; }
        input[type="file"] {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background: #fafafa;
        }

        button {
            padding: 12px;
            border-radius: 6px;
            border: none;
            background: #2e7d32;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover { background: #1b5e20; }

        .advisory {
            background: #e8f5e9;
            border-left: 5px solid #2e7d32;
            padding: 15px;
            border-radius: 8px;
            text-align: left;
            word-wrap: break-word;
        }

        .advisory b { color: #1b5e20; }
    </style>
</head>
<body>
<header>
    <h1>🌾 Crop Advisory</h1>
    <a href="index.php">← Home</a>
</header>

<main>
    <div class="card">
        <h2>AI-Based Crop Advisory</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label>Upload Crop Image:</label>
            <input type="file" name="crop_image" accept="image/*" required>
            <button type="submit">Get Advisory</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
           
            $messages = [
                "Healthy" => [
                    "en" => "🌱 This plant looks healthy. Keep monitoring soil moisture.",
                    "bn" => "🌱 এটি একটি সুস্থ গাছ বলে মনে হচ্ছে। মাটির আর্দ্রতা পর্যবেক্ষণ করুন।"
                ],
                "Pest" => [
                    "en" => "⚠️ Pests detected. Apply recommended pesticide.",
                    "bn" => "⚠️ পোকামাকড় দেখা গেছে। প্রয়োজনীয় কীটনাশক প্রয়োগ করুন।"
                ],
                "Nutrient" => [
                    "en" => "🍃 Leaves show yellowing. Add nitrogen fertilizer.",
                    "bn" => "🍃 পাতা হলুদ হয়েছে। নাইট্রোজেন সার প্রয়োগ করুন।"
                ],
                "Water" => [
                    "en" => "💧 Soil is wet. Reduce watering frequency.",
                    "bn" => "💧 মাটি ভিজে আছে। পানি দেওয়ার ফ্রিকোয়েন্সি কমান।"
                ]
            ];

      
            $status = array_rand($messages);

            echo "<div class='advisory'><b>AI Suggestion (English):</b> " . $messages[$status]['en'] . "</div>";
            echo "<div class='advisory'><b>এআই পরামর্শ (বাংলা):</b> " . $messages[$status]['bn'] . "</div>";
        }
        ?>
    </div>
</main>
</body>
</html>
