<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

$success_msg = '';
$error_msg = '';

// ===== Handle Add/Update Farmer =====
if(isset($_POST['save_farmer'])){
    $farmer_id = intval($_POST['farmer_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $location = $conn->real_escape_string($_POST['location']);

    $check_farmer = $conn->query("SELECT id FROM farmers WHERE id=$farmer_id");
    if($check_farmer->num_rows > 0){
        $sql = "UPDATE farmers SET name='$name', phone='$phone', location='$location' WHERE id=$farmer_id";
    } else {
        $sql = "INSERT INTO farmers (id, name, phone, location) VALUES ($farmer_id,'$name','$phone','$location')";
    }

    if($conn->query($sql)){
        $success_msg = "✅ Farmer saved successfully!";
    } else {
        $error_msg = "❌ Farmer Error: " . $conn->error;
    }
}

// ===== Handle Add/Update Crop =====
if(isset($_POST['save_crop'])){
    $crop_id = intval($_POST['crop_id']);
    $farmer_id = intval($_POST['farmer_id']);
    $crop_name = $conn->real_escape_string($_POST['crop_name']);
    $price = $conn->real_escape_string($_POST['price']);
    $quantity = $conn->real_escape_string($_POST['quantity']);

    $check_farmer = $conn->query("SELECT id FROM farmers WHERE id=$farmer_id");
    if($check_farmer->num_rows == 0){
        $error_msg = "❌ Selected Farmer does not exist!";
    } else {
        $image = null;
        if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
            $image = $_FILES['image']['name'];
            $target = "uploads/" . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        }

        if($crop_id > 0){
            $update_img_sql = $image ? ", image='$image'" : "";
            $sql = "UPDATE crops SET farmer_id=$farmer_id, crop_name='$crop_name', price='$price', quantity='$quantity' $update_img_sql WHERE crop_id=$crop_id";
        } else {
            $sql = "INSERT INTO crops (farmer_id, crop_name, price, quantity, image) VALUES ($farmer_id,'$crop_name','$price','$quantity','$image')";
        }

        if($conn->query($sql)){
            $success_msg = "✅ Crop saved successfully!";
        } else {
            $error_msg = "❌ Crop Error: " . $conn->error;
        }
    }
}

// ===== Fetch Farmers =====
$farmer_list = [];
$res = $conn->query("SELECT * FROM farmers ORDER BY id ASC");
while($row = $res->fetch_assoc()) $farmer_list[] = $row;

// ===== Fetch Crops =====
$crops_list = [];
$res2 = $conn->query("SELECT crops.*, farmers.name as farmer_name FROM crops JOIN farmers ON crops.farmer_id=farmers.id ORDER BY crop_id DESC");
while($row = $res2->fetch_assoc()) $crops_list[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Farmers & Crops - Smart Agriculture</title>
<link rel="stylesheet" href="css/styles.css">
<style>
body {
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background:#f0f4f7;
    margin:0;
    color:#333;
}
header {
    background:#2e7d32;
    color:white;
    padding:20px;
    text-align:center;
}
header a {
    color:white;
    text-decoration:none;
    background:#27ae60;
    padding:8px 15px;
    border-radius:5px;
    display:inline-block;
    margin-top:10px;
}
header a:hover { background:#1b5e20; }
main {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
    padding:30px;
    max-width:1100px;
    margin:auto;
}
.card {
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 6px 15px rgba(0,0,0,0.1);
    transition:transform 0.2s ease;
}
.card:hover { transform:translateY(-3px); }
h2 {
    color:#2e7d32;
    text-align:center;
    margin-bottom:15px;
}
form { display:grid; gap:15px; }
label { font-weight:bold; }
input, select, button {
    padding:12px;
    border-radius:6px;
    border:1px solid #ccc;
    width:100%;
}
button {
    background:#2e7d32;
    color:white;
    border:none;
    cursor:pointer;
    font-weight:bold;
}
button:hover { background:#1b5e20; }
.message {
    padding:10px;
    border-radius:5px;
    margin-top:10px;
    text-align:center;
}
.success { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }
@media(max-width:900px){ main{grid-template-columns:1fr;} }

/* ✅ New styles for Existing Crops section */
.existing-crops {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}
.crop-item {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}
.crop-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.15);
}
.crop-item img {
    width: 100%;
    height: 200px; /* ✅ bigger image */
    object-fit: cover;
}
.crop-info {
    padding: 15px;
}
.crop-info h3 {
    margin: 0;
    font-size: 18px;
    color: #27ae60;
}
.crop-info p {
    margin: 6px 0;
    font-size: 14px;
    color: #555;
}
</style>
</head>
<body>

<header>
<h1>👨‍🌾 Manage Farmers & Crops</h1>
<a href="marketplace.php">→ Go to Marketplace</a>
</header>

<main>
<!-- Add / Update Farmer -->
<div class="card">
<h2>Add / Update Farmer</h2>
<?php if($success_msg) echo "<div class='message success'>$success_msg</div>"; ?>
<?php if($error_msg) echo "<div class='message error'>$error_msg</div>"; ?>
<form method="post">
    <label>Farmer ID (Manual)</label>
    <input type="number" name="farmer_id" required placeholder="Enter Farmer ID">

    <label>Farmer Name</label>
    <input type="text" name="name" required>

    <label>Phone</label>
    <input type="text" name="phone" required>

    <label>Location</label>
    <input type="text" name="location" required>

    <button type="submit" name="save_farmer">Save Farmer</button>
</form>
</div>

<!-- Add / Update Crop -->
<div class="card">
<h2>Add / Update Crop</h2>
<form method="post" enctype="multipart/form-data">
    <label>Select Farmer</label>
    <select name="farmer_id" required>
        <option value="">-- Select Farmer --</option>
        <?php foreach($farmer_list as $f){
            echo "<option value='{$f['id']}'>{$f['name']} (ID: {$f['id']})</option>";
        } ?>
    </select>

    <label>Crop Name</label>
    <input type="text" name="crop_name" required>

    <label>Price</label>
    <input type="text" name="price" required>

    <label>Quantity</label>
    <input type="text" name="quantity" required>

    <label>Image</label>
    <input type="file" name="image">

    <input type="hidden" name="crop_id" value="0">
    <button type="submit" name="save_crop">Save Crop</button>
</form>
</div>

<!-- ✅ Existing Crops Section -->
<div class="card" style="grid-column:1 / -1;">
<h2>🌾 Existing Crops</h2>
<div class="existing-crops">
<?php
if(count($crops_list) > 0){
    foreach($crops_list as $c){
        $img_path = $c['image'] && file_exists("uploads/".$c['image']) ? "uploads/".$c['image'] : "uploads/no_image.jpg";
        echo "<div class='crop-item'>";
        echo "<img src='{$img_path}' alt='{$c['crop_name']}'>";
        echo "<div class='crop-info'>";
        echo "<h3>{$c['crop_name']}</h3>";
        echo "<p><strong>Farmer:</strong> {$c['farmer_name']}</p>";
        echo "<p><strong>Price:</strong> ₹{$c['price']}</p>";
        echo "<p><strong>Quantity:</strong> {$c['quantity']}</p>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p style='text-align:center;'>No crops added yet.</p>";
}
?>
</div>
</div>

</main>
</body>
</html>
