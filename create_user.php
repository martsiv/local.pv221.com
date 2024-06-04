<?php
// Database credentials
$config = require 'config.php';

$host = $config['db_host'];
$dbname = $config['db_name'];
$username = $config['db_user'];
$password = $config['db_pass'];

// Connection string
$dsn = "mysql:host=$host;dbname=$dbname";
$options = [];

function redirect($url) {
    header('Location: '.$url);
    die();
}

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Downloading image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $imageName = time() . '_' . $image['name'];
        $targetDirectory = 'uploads/';
        $targetFile = $targetDirectory . basename($imageName);

        // Check format image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            // Downloading image to server
            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                // Inserting data into the database
                $sql = 'INSERT INTO tbl_users (name, email, phone, image) VALUES (:name, :email, :phone, :image)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'image' => $targetFile]);

                echo 'User created successfully.';
                redirect("/index.php");
            } else {
                echo 'Failed to upload image.';
            }
        } else {
            echo 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
        }
    } else {
        echo 'Image upload error.';
    }
}
?>