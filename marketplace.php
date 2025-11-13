<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

$PRIVATE_KEY = "sh14z";

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: marketplace.php");
    exit;
}

if (isset($_POST['private_key'])) {
    $entered_key = trim($_POST['private_key']);
    if ($entered_key === $PRIVATE_KEY) {
        $_SESSION['auth'] = true;

        if (isset($_SESSION['pending_delete_id'])) {
            $delete_id = intval($_SESSION['pending_delete_id']);
            unset($_SESSION['pending_delete_id']);

            $img_query = "SELECT image FROM crops WHERE crop_id = $delete_id";
            $img_result = $conn->query($img_query);
            if ($img_result && $img_result->num_rows > 0) {
                $img_row = $img_result->fetch_assoc();
                $image_path = 'uploads/' . $img_row['image'];
                if (file_exists($image_path)) unlink($image_path);
            }

            $conn->query("DELETE FROM crops WHERE crop_id = $delete_id");
            echo "<script>alert('✅ ফসল সফলভাবে ডিলিট হয়েছে'); window.location='marketplace.php';</script>";
            exit;
        } else {
            echo "<script>alert('✅ অ্যাক্সেস অনুমোদিত হয়েছে'); window.location='marketplace.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('❌ ভুল প্রাইভেট কী'); window.location='marketplace.php';</script>";
        exit;
    }
}

if (isset($_GET['delete_id'])) {
    if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
        $_SESSION['pending_delete_id'] = $_GET['delete_id'];
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <title>Enter Private Key</title>
        <style>
            body { font-family: Arial; background:#f0f0f0; display:flex; align-items:center; justify-content:center; height:100vh; }
            .confirm-box { background:white; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.2); width:350px; text-align:center; }
            input { width:90%; padding:10px; margin-top:10px; border:1px solid #ccc; border-radius:5px; }
            button { margin-top:15px; padding:10px 20px; background:#2e7d32; color:white; border:none; border-radius:5px; cursor:pointer; }
            button:hover { background:#1b5e20; }
        </style>
        </head>
        <body>
        <div class="confirm-box">
            <h3>🔒 Enter Private Key</h3>
            <form method="POST">
                <input type="password" name="private_key" placeholder="Enter private key" required>
                <button type="submit">Confirm Access</button>
            </form>
        </div>
        </body>
        </html>
        <?php
        exit;
    }

    $delete_id = intval($_GET['delete_id']);
    $img_query = "SELECT image FROM crops WHERE crop_id = $delete_id";
    $img_result = $conn->query($img_query);
    if ($img_result && $img_result->num_rows > 0) {
        $img_row = $img_result->fetch_assoc();
        $image_path = 'uploads/' . $img_row['image'];
        if (file_exists($image_path)) unlink($image_path);
    }

    $conn->query("DELETE FROM crops WHERE crop_id = $delete_id");
    echo "<script>alert('✅ successfully deleted'); window.location='marketplace.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Marketplace - Smart Agriculture</title>
<link rel="stylesheet" href="styles.css">
<style>
body { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:linear-gradient(135deg,#eafaf1,#ffffff); margin:0; padding:0; color:#333; }
header { background:linear-gradient(135deg,#2e7d32,#1b5e20); color:white; padding:30px 20px; text-align:center; box-shadow:0 4px 8px rgba(0,0,0,0.2); }
header h1 { margin:0; font-size:34px; letter-spacing:1px; }
nav { background-color:#2ecc71; display:flex; justify-content:center; flex-wrap:wrap; padding:12px 0; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
nav a { color:white; padding:12px 25px; margin:5px 8px; border-radius:25px; font-weight:600; font-size:16px; transition:all 0.3s ease; text-decoration:none; }
nav a:hover { background-color:#27ae60; transform:translateY(-3px); text-decoration:none; }
nav a.active { background-color:#1b5e20; box-shadow:0 4px 10px rgba(0,0,0,0.2); }
main { padding:40px 20px; animation:fadeIn 0.8s ease; }
h2 { text-align:center; color:#2e7d32; margin-bottom:30px; font-size:28px; }
.market-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:25px; padding:10px; }
.crop-card { background-color:white; padding:20px; border-radius:15px; box-shadow:0 6px 15px rgba(0,0,0,0.1); text-align:center; transition:transform 0.3s, box-shadow 0.3s; position:relative; }
.crop-card:hover { transform:translateY(-8px) scale(1.02); box-shadow:0 12px 25px rgba(0,0,0,0.2); }
.crop-card img { width:100%; max-width:260px; height:220px; object-fit:cover; border-radius:10px; margin-bottom:15px; }
.crop-card h3 { margin:10px 0 8px; color:#27ae60; font-size:20px; }
.crop-card p { color:#555; margin:6px 0; font-size:15px; }
.contact-btns { display:flex; justify-content:center; gap:10px; margin-top:10px; flex-wrap:wrap; }
.call-btn, .sms-btn, .wa-btn { display:inline-block; padding:10px 18px; border-radius:6px; color:white; font-weight:600; font-size:15px; text-decoration:none; transition:0.3s; }
.call-btn { background:#27ae60; }
.call-btn:hover { background:#1b5e20; transform:translateY(-3px); }
.sms-btn { background:#2980b9; }
.sms-btn:hover { background:#1b4f72; transform:translateY(-3px); }
.wa-btn { background:#25d366; }
.wa-btn:hover { background:#128c7e; transform:translateY(-3px); }
.delete-btn { background-color:#e74c3c; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer; font-size:14px; margin-top:8px; transition:0.3s; }
.delete-btn:hover { background-color:#c0392b; }
footer { background-color:#2c3e50; color:white; text-align:center; padding:25px; margin-top:50px; font-size:16px; box-shadow:0 -3px 8px rgba(0,0,0,0.1); }
@keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
</style>
</head>
<body>
<header><h1>🌱 Smart Agriculture Marketplace</h1></header>
<nav>
<?php $current = basename($_SERVER['PHP_SELF']); ?>
<a href="index.php" class="<?= $current=='index.php'?'active':'' ?>">Home</a>
<a href="add_crop.php" class="<?= $current=='add_crop.php'?'active':'' ?>">Add Crop</a>
<a href="marketplace.php" class="<?= $current=='marketplace.php'?'active':'' ?>">Marketplace</a>
<a href="weather.php" class="<?= $current=='weather.php'?'active':'' ?>">Weather</a>
<a href="advisory.php" class="<?= $current=='advisory.php'?'active':'' ?>">Advisory</a>
<a href="marketplace.php?logout=1" style="background:#c0392b;">Logout</a>
</nav>
<main>
<h2>Available Crops</h2>
<div class="market-grid">
<?php
$sql="SELECT crops.*, farmers.name AS farmer_name, farmers.phone AS farmer_phone, farmers.location AS farmer_location FROM crops JOIN farmers ON crops.farmer_id=farmers.id";
$result=$conn->query($sql);
if($result && $result->num_rows>0){
    while($row=$result->fetch_assoc()){
        $image_path='uploads/'.$row['image'];
        if(!file_exists($image_path) || empty($row['image'])) $image_path='uploads/no_image.jpg';
        echo "<div class='crop-card'>";
        echo "<img src='{$image_path}' alt='{$row['crop_name']}'>";
        echo "<h3>{$row['crop_name']}</h3>";
        echo "<p><strong>Farmer:</strong> {$row['farmer_name']}</p>";
        echo "<p><strong>Location:</strong> {$row['farmer_location']}</p>";
        echo "<p><strong>Price:</strong> ₹{$row['price']}</p>";
        echo "<p><strong>Quantity:</strong> {$row['quantity']}</p>";
        
        // Contact buttons
        echo "<div class='contact-btns'>";
        echo "<a href='tel:{$row['farmer_phone']}' class='call-btn'>Call</a>";
        echo "<a href='sms:{$row['farmer_phone']}' class='sms-btn'>SMS</a>";
        echo "<a href='https://wa.me/{$row['farmer_phone']}' target='_blank' class='wa-btn'>WhatsApp</a>";
        echo "</div>";

        // Delete button
        echo "<a href='marketplace.php?delete_id={$row['crop_id']}' onclick=\"return confirm('Are you sure you want to delete this crop?');\">";
        echo "<button class='delete-btn'>Delete</button></a>";
        echo "</div>";
    }
}else{
    echo "<p style='text-align:center'>No crops available.</p>";
}
?>
</div>
</main>
<footer>Smart Agriculture</footer>
</body>
</html>
