<?php
include $_SERVER['DOCUMENT_ROOT'] . '/connection_database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Початок SQL транзакції
    $pdo->beginTransaction();

    try {
        // Отримання старого зображення для видалення, якщо буде завантажено нове
        $sql_select = 'SELECT image FROM tbl_users WHERE id = :id';
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->execute(['id' => $user_id]);
        $user = $stmt_select->fetch(PDO::FETCH_ASSOC);

        // Оновлення користувача в базі даних
        $sql_update = 'UPDATE tbl_users SET name = :name, email = :email, phone = :phone WHERE id = :id';
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'id' => $user_id,
        ]);

        // Обробка завантаженого зображення
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . UPLOADS;

            // Переміщення завантаженого файлу в директорію
            if (move_uploaded_file($tmp_name, "$upload_dir/$image")) {
                // Оновлення шляху до зображення в базі даних
                $sql_update_image = 'UPDATE tbl_users SET image = :image WHERE id = :id';
                $stmt_update_image = $pdo->prepare($sql_update_image);
                $stmt_update_image->execute([
                    'image' => $image,
                    'id' => $user_id,
                ]);

                // Видалення старого зображення
                if ($user['image'] && file_exists("$upload_dir/{$user['image']}")) {
                    unlink("$upload_dir/{$user['image']}");
                }
            } else {
                throw new Exception('Failed to move uploaded file');
            }
        }

        // Підтвердження транзакції
        $pdo->commit();
        echo json_encode(['success' => 'User updated successfully']);
    } catch (Exception $e) {
        // Відміна транзакції у разі помилки
        $pdo->rollBack();
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
