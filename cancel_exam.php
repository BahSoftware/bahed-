<?php
include("inc/data.php");

$examId = $_POST['exam_id'] ?? '';
$userId = $_POST['user_id'] ?? ''; // Kullanıcı ID'sini almak için

if (empty($examId) || empty($userId)) {
    echo "Geçersiz sınav ID veya kullanıcı ID.";
    exit();
}

try {
    // Veritabanı bağlantısını kontrol et
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sınav iptali kaydını ekle
    $sql = "INSERT INTO exam_cancellations (exam_id, user_id, cancellation_reason) VALUES (:exam_id, :user_id, 'Sınav sayfasından çıkıldığı için iptal edildi')";
    $stmt = $db->prepare($sql);
    $stmt->execute(['exam_id' => $examId, 'user_id' => $userId]);

    echo "Sınav iptal kaydı başarılı.";
} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
    exit();
}
?>
