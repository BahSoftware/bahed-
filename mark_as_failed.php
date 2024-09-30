<?php
include("inc/data.php"); // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $haberId = $_POST['haber_id'] ?? '';

    if ($userId && $haberId) {
        $updateSql = "UPDATE user_progress SET completed = FALSE, reason = 'Time expired' WHERE user_id = :user_id AND haber_id = :haber_id";
        $stmt = $db->prepare($updateSql);
        $stmt->execute([
            'user_id' => $userId,
            'haber_id' => $haberId
        ]);
    }
}
?>
