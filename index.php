<?php
include $_SERVER['DOCUMENT_ROOT'] . '/connection_database.php';
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
                $image = "/".UPLOADS."/".$row['image'];
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
                    <a href='' class=delete-link data-user-id=$id>Delete</a>
                </td>

            </tr>
                ";
            }
            ?>
            </tbody>
        </table>
    </div>
</main>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteUserId;

            document.querySelectorAll('.delete-link').forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    deleteUserId = this.getAttribute('data-user-id');
                    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                // Create new object FormData
                var formData = new FormData();

                // Add data to formData
                formData.append('user_id', deleteUserId);
                axios.post("/delete.php", formData)
                    .then(resp => {
                        console.log("Deleting success");
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error deleting:', error);
                    });
            });
        });
    </script>

</body>
</html>
