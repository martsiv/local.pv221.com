<?php
$errors = [];
$name = $email = $phone = $image = '';

if($_SERVER["REQUEST_METHOD"]=="POST") {
    include_once $_SERVER["DOCUMENT_ROOT"]."/connection_database.php";

    // Check field name
    if (empty($_POST["name"])) {
        $errors['name'] = "Please choose a name.";
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    // Check field email
    if (empty($_POST["email"])) {
        $errors['email'] = "Please choose an email.";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    // Check field phone
    if (empty($_POST["phone"])) {
        $errors['phone'] = "Please choose a phone.";
    } else {
        $phone = htmlspecialchars($_POST["phone"]);
    }

    // Check field image
    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] != UPLOAD_ERR_OK) {
        $errors['image'] = "Please choose a picture.";
    } else {
        $folderName = $_SERVER['DOCUMENT_ROOT'].'/'. UPLOADS; // Set folders name for images
        if (!file_exists($folderName)) {
            mkdir($folderName, 0777); // Create folder with access 0777
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' .$ext;
        $uploadfile = $folderName ."/". $fileName;

        // Check file size
        if ($_FILES["image"]["size"] > 500000) {
            $errors['image'] = "Sorry, your file is too large.";
        }

        // Check allowed file formats
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array(strtolower($ext), $allowed_types)) {
            $errors['image'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // If no errors - upload file
        if (empty($errors['image'])) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $uploadfile)) {
                $errors['image'] = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Check for email and phone uniqueness
    if (empty($errors)) {
        // Check for email uniqueness
        $sql = 'SELECT COUNT(*) FROM tbl_users WHERE email = :email';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $errors['email'] = "Email already exists.";
        }

        // Check for phone uniqueness
        $sql = 'SELECT COUNT(*) FROM tbl_users WHERE phone = :phone';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['phone' => $phone]);
        if ($stmt->fetchColumn() > 0) {
            $errors['phone'] = "Phone already exists.";
        }
    }

    // If no errors - save data to database
    if (empty($errors)) {
        $sql = 'INSERT INTO tbl_users (name, email, phone, image) VALUES (:name, :email, :phone, :image)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'image' => $fileName]);

        header('Location: /');
        exit();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="/index.php" class="nav-link" aria-current="page">Users</a></li>
            <li class="nav-item"><a href="/create.php" class="nav-link active">Crete user</a></li>
        </ul>
    </header>
</div>
    <main>
        <div class="container">
            <h1 class="text-center">Create New User</h1>
            <form class="col-md-6 offset-md-3 needs-validation" novalidate method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['name'] ?? 'Please choose a name.'; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['email'] ?? 'Please choose an email.'; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="text" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" required>
                    <div class="invalid-feedback">
                        <?php echo $errors['phone'] ?? 'Please choose a phone.'; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-3">
                            <label for="image" class="form-label">
                                <img src="/images/no-photo.jpg" alt="фото" width="100%">
                            </label>
                        </div>
                        <div class="mb-3 col-md-9">
                            <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image" aria-describedby="emailHelp" required>
                            <div class="invalid-feedback">
                                <?php echo $errors['image'] ?? 'Please choose a picture.'; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success me-2">Create</button>
                    <a href="/" class="btn btn-primary">Cancel</a>
                </div>
            </form>
        </div>
    </main>
    <script src="/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })

        // Remove 'is-invalid' class on input change
        var inputs = document.querySelectorAll('input')

        Array.prototype.slice.call(inputs)
            .forEach(function (input) {
                input.addEventListener('input', function () {
                    if (input.classList.contains('is-invalid')) {
                        input.classList.remove('is-invalid')
                    }
                }, false)
            })
    })()
</script>
</body>
</html><?php
