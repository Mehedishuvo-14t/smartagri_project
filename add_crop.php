<?php
session_start();
include 'config.php';

$success_msg = '';
$error_msg   = '';

/* ===============================
   FETCH FARMERS
================================ */
$farmer_list = [];
$res = $conn->query("SELECT * FROM farmers ORDER BY id ASC");
while($row = $res->fetch_assoc()){
    $farmer_list[] = $row;
}

/* ===============================
   ADD / UPDATE CROP (PASSWORD REQUIRED)
================================ */
if(isset($_POST['save_crop'])){
    $farmer_id = intval($_POST['farmer_id']);
    $crop_name = $conn->real_escape_string($_POST['crop_name']);
    $price     = $conn->real_escape_string($_POST['price']);
    $quantity_input = $_POST['quantity'];
    $password  = $_POST['password'];

    if(!is_numeric($quantity_input) || floatval($quantity_input) < 0){
        $error_msg = "❌ Quantity must be a positive number!";
    } else {

        // 🔐 verify farmer first
        $f = $conn->query("SELECT * FROM farmers WHERE id=$farmer_id");
        if(!$f || $f->num_rows !== 1){
            $error_msg = "❌ Farmer not found!";
        } else {
            $farmer = $f->fetch_assoc();

            if(!password_verify($password, $farmer['password'])){
                $error_msg = "❌ Wrong password!";
            } else {

                $quantity = floatval($quantity_input);
                $image = null;

                $check_sql = "SELECT * FROM crops 
                              WHERE farmer_id=$farmer_id 
                              AND crop_name='$crop_name'";
                $check_res = $conn->query($check_sql);

                if(!empty($_FILES['image']['name'])){
                    $image = time().'_'.basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
                }

                if($check_res && $check_res->num_rows > 0){
                    // UPDATE
                    $row = $check_res->fetch_assoc();
                    $update_sql = "UPDATE crops 
                                   SET price='$price', quantity='$quantity'";

                    if($image){
                        if($row['image'] && file_exists("uploads/".$row['image'])){
                            unlink("uploads/".$row['image']);
                        }
                        $update_sql .= ", image='$image'";
                    }

                    $update_sql .= " WHERE crop_id=".$row['crop_id'];

                    if($conn->query($update_sql)){
                        $success_msg = "✅ Crop updated successfully!";
                    } else {
                        $error_msg = "❌ Update failed!";
                    }

                } else {
                    // INSERT
                    $sql = "INSERT INTO crops (farmer_id,crop_name,price,quantity,image)
                            VALUES ($farmer_id,'$crop_name','$price','$quantity','$image')";
                    if($conn->query($sql)){
                        $success_msg = "✅ Crop added successfully!";
                    } else {
                        $error_msg = "❌ Insert failed!";
                    }
                }
            }
        }
    }
}

/* ===============================
   DELETE CROP (PASSWORD REQUIRED)
================================ */
if(isset($_POST['delete_crop'])){
    $farmer_id = intval($_POST['del_farmer_id']);
    $crop_name = $conn->real_escape_string($_POST['del_crop_name']);
    $password  = $_POST['password'];

    $f = $conn->query("SELECT * FROM farmers WHERE id=$farmer_id");
    if($f && $f->num_rows === 1){
        $farmer = $f->fetch_assoc();

        if(password_verify($password, $farmer['password'])){
            $c = $conn->query("SELECT * FROM crops 
                               WHERE farmer_id=$farmer_id 
                               AND crop_name='$crop_name'");

            if($c && $c->num_rows === 1){
                $crop = $c->fetch_assoc();

                if($crop['image'] && file_exists("uploads/".$crop['image'])){
                    unlink("uploads/".$crop['image']);
                }

                $conn->query("DELETE FROM crops WHERE crop_id=".$crop['crop_id']);
                $success_msg = "🗑️ Crop deleted successfully!";
            } else {
                $error_msg = "❌ Crop not found!";
            }
        } else {
            $error_msg = "❌ Wrong password!";
        }
    } else {
        $error_msg = "❌ Farmer not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Crop Management</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma}
body{background:linear-gradient(135deg,#fffde7,#e8f5e9);padding-top:100px}

/* NAVBAR */
nav{
    position:fixed;top:0;left:0;width:100%;
    background:linear-gradient(90deg,#1b5e20,#2e7d32);
    color:#fff;padding:22px 40px;
    display:flex;justify-content:space-between;align-items:center;
    box-shadow:0 8px 25px rgba(0,0,0,0.25);
}
nav div:first-child{font-size:26px;font-weight:700}
nav a{
    color:white;text-decoration:none;margin-left:22px;
    font-size:18px;font-weight:600;padding:8px 14px;
    border-radius:8px;transition:.3s
}
nav a:hover{background:rgba(255,255,255,.15)}

@media(max-width:768px){
    nav{flex-direction:column;gap:12px}
}

.container{max-width:650px;margin:auto;display:grid;gap:30px}
.card{
    background:#fff;padding:30px;border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,.2)
}
h2{text-align:center;color:#2e7d32;margin-bottom:15px}
input,select,button{
    width:100%;padding:12px;margin:10px 0;
    border-radius:8px;border:1px solid #ccc
}
button{background:#2e7d32;color:#fff;border:none;font-size:16px}
button.delete{background:#c62828}
.success{background:#c8e6c9;padding:12px;border-radius:8px}
.error{background:#ffcdd2;padding:12px;border-radius:8px}
</style>
</head>

<body>

<nav>
    <div>🌾 Farmer Portal</div>
    <div>
        <a href="index.php">Home</a>
        <a href="login.php">Farmer Profile</a>
        <a href="marketplace.php">Marketplace</a>
    </div>
</nav>

<div class="container">

<?php if($success_msg) echo "<div class='success'>$success_msg</div>"; ?>
<?php if($error_msg) echo "<div class='error'>$error_msg</div>"; ?>

<!-- ADD / UPDATE -->
<div class="card">
<h2>Add / Update Crop</h2>
<form method="post" enctype="multipart/form-data">
<select name="farmer_id" required>
<option value="">Select Farmer</option>
<?php foreach($farmer_list as $f){
echo "<option value='{$f['id']}'>{$f['name']}</option>";
} ?>
</select>

<input type="text" name="crop_name" placeholder="Crop Name" required>
<input type="text" name="price" placeholder="Price" required>
<input type="number" name="quantity" step="0.01" placeholder="Quantity" required>

<input type="password" name="password" placeholder="Farmer Login Password" required>

<input type="file" name="image">
<button name="save_crop">Save Crop</button>
</form>
</div>

<!-- DELETE -->
<div class="card">
<h2>Delete Crop</h2>
<form method="post">
<select name="del_farmer_id" required>
<option value="">Select Farmer</option>
<?php foreach($farmer_list as $f){
echo "<option value='{$f['id']}'>{$f['name']}</option>";
} ?>
</select>

<input type="text" name="del_crop_name" placeholder="Crop Name" required>
<input type="password" name="password" placeholder="Farmer Login Password" required>
<button class="delete" name="delete_crop">Delete Crop</button>
</form>
</div>

</div>
</body>
</html>
