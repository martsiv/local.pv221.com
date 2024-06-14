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
            <li class="nav-item"><a href="/create.php" class="nav-link">Create user</a></li>
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
                $image = "/" . UPLOADS . "/" . $row['image'];
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
                    <button class='btn btn-primary edit-btn' data-toggle='modal' data-target='#editModal' data-user-id='$id'>
                        Edit
                    </button>
                    <a href='' class='delete-link' data-user-id='$id'>Delete</a>
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

<!-- Edit user modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="user_id">

                    <div class="mb-3">
                        <label for="editName" class="form-label">Name:</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone:</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label for="editImage" class="form-label">Image:</label>
                                <img id="editImagePreview" src="/images/no-photo.jpg" alt="Photo" class="img-fluid">
                            </div>
                            <div class="col-md-9">
                                <input type="file" class="form-control" id="editImage" name="image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
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

        // Functionality for Edit User
        var editUserId;

        // Edit button click handler
        document.querySelectorAll('.edit-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                editUserId = this.getAttribute('data-user-id');
                console.log('Edit button clicked for user id:', editUserId); // Debug log
                // Очищення форми перед відкриттям модального вікна
                document.getElementById('editForm').reset();
                // Заповнення форми з даними користувача
                fillEditForm(editUserId);
                // Показ модального вікна
                var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });

        // Function for filling out a form with user data
        function fillEditForm(userId) {
            axios.get('/get_user.php?id=' + userId)
                .then(function(response) {
                    var user = response.data;
                    console.log('User data:', user); // Debug log
                    if (user.error) {
                        console.error(user.error);
                        return;
                    }
                    document.getElementById('editUserId').value = userId;
                    document.getElementById('editName').value = user.name;
                    document.getElementById('editEmail').value = user.email;
                    document.getElementById('editPhone').value = user.phone;
                    document.getElementById('editImagePreview').src = '/uploads/' + user.image;
                })
                .catch(function(error) {
                    console.error('Error fetching user data:', error);
                });
        }

        // Edit form submit event handler
        document.getElementById('editForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            axios.post('/update_user.php', formData)
                .then(function(response) {
                    console.log('User updated successfully');
                    location.reload();
                })
                .catch(function(error) {
                    console.error('Error updating user:', error);
                });
        });

        // File change handler for updating the preview image
        document.getElementById('editImage').addEventListener('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('editImagePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
</body>
</html>
