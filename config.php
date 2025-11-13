<?php

$host = "localhost:3306";      
            
$user = "root";           
$pass = "";               
$dbname = "smartagri";    


$conn = new mysqli($host, $user, $pass, $dbname);


if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
//define('HF_API_TOKEN', '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');
?>
