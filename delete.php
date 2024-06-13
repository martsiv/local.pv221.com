<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    include_once $_SERVER["DOCUMENT_ROOT"]."/connection_database.php";

    // Get file name to delete
    $sql_select_image = "SELECT image FROM tbl_users WHERE id=:id";
    $stmt_select_image = $pdo->prepare($sql_select_image);
    $stmt_select_image->execute(['id' => $user_id]);
    $image_row = $stmt_select_image->fetch(PDO::FETCH_ASSOC);

    if ($image_row) {
        $image_filename = $image_row['image'];

        // Delete image from server
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $image_filename;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete row from databese
    $sql_delete_user = "DELETE FROM tbl_users WHERE id=:id";
    $stmt_delete_user = $pdo->prepare($sql_delete_user);
    $stmt_delete_user->execute(['id' => $user_id]);

    if ($stmt_delete_user->rowCount() > 0) {
        echo "Record and image deleted successfully!";
    } else {
        echo "Error deleting record.";
    }
    exit();
}
?>
