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

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    // Checking if the $user_id parameter is passed
    if (!empty($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);

        // Checking if the user has confirmed the deletion
        if (!empty($_GET['confirm'])) {
            // Deleting a record from the database
            $sql = "DELETE FROM tbl_users WHERE id=$user_id";
            $pdo->query($sql);
            echo "Record deleted successfully!";
            header('Location: '."/index.php");
            die();
        } else {
            // Deletion confirmation output
            echo "Are you sure you want to delete this entry??<br>";
            echo "<a href='delete.php?user_id=$user_id&confirm=1'>Yes</a> - ";
            echo "<a href='index.php'>No</a>";
        }
    } else {
        echo "Incorrect incoming parameter user_id.";
    }
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
