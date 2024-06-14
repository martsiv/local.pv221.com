<?php
include $_SERVER['DOCUMENT_ROOT'] . '/connection_database.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    $sql = 'SELECT * FROM tbl_users WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
