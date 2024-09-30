<?php
include("inc/data.php"); // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $haberId = $_POST['haber_id'] ?? '';

    if ($userId && $haberId) {
        // Eğitimi başlatma zamanını güncelle
        $updateSql = "INSERT INTO user_progress (user_id, haber_id, completed, start_time) 
                      VALUES (:user_id, :haber_id, TRUE, NOW())
                      ON DUPLICATE KEY UPDATE completed = TRUE, start_time = NOW()";
        $stmt = $db->prepare($updateSql);
        $stmt->execute([
            'user_id' => $userId,
            'haber_id' => $haberId
        ]);
    }
}
?>
