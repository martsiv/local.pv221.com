<?php
// Database credentials
$config = require 'config.php';

$host = $config['db_host'];
$dbname = $config['db_name'];
$username = $config['db_user'];
$password = $config['db_pass'];

// Connection string
$dsn = "mysql:host=$host;dbname=$dbname";

// Attempt to connect
try {
    $pdo = new PDO($dsn, $username, $password);
    // Set PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
function deteleRecord($id, $pdo){
    // sql to delete a record
    $sql = "DELETE FROM tbl_users WHERE id=$id";
    $pdo->query($sql);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Users</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <header class="d-flex justify-content-center py-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="/index.php" class="nav-link active" aria-current="page">Users</a></li>
                <li class="nav-item"><a href="/create.php" class="nav-link">Crete user</a></li>
            </ul>
        </header>
    </div>
<main>
    <div class="container">
       <h1>Users</h1>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Photo</th>
                <th scope="col">User Info</th>
                <th scope="col">Phone</th>
                <th scope="col">Email</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql = 'SELECT * FROM tbl_users';
            foreach ($pdo->query($sql) as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $email = $row['email'];
                $phone = $row['phone'];
                $image = $row['image'];
                echo "
            <tr>
                <th scope='row'>$id</th>
                <td>
                    <img src='$image'
                         width='150'
                         alt='$name'>
                </td>
                <td>$name</td>
                <td>$phone</td>
                <td>$email</td>
                <td>
                    <a href='delete.php?user_id=$id'>Delete</a>
                </td>

            </tr>
                ";
            }
            ?>
            </tbody>
        </table>
    </div>
</main>

<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php