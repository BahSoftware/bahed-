<?php
include("inc/data.php"); // Veritabanı bağlantısı

header('Content-Type: application/json');

// POST verilerini al
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$courseId = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if ($userId > 0 && $courseId > 0) {
    try {
        // Şu anki zamanı al
        $currentTime = date('Y-m-d H:i:s');
        $endTime = date('Y-m-d H:i:s', strtotime($currentTime . ' + 40 minutes'));

        // Kullanıcının bu kursu başlatmış olup olmadığını kontrol et
        $stmt = $db->prepare("SELECT start_time, end_time FROM course_start_times WHERE user_id = :user_id AND course_id = :course_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            // Kullanıcı zaten bu kursu başlatmış, kontrol yap
            $startTime = $record['start_time'];
            $storedEndTime = $record['end_time'];

            if ($currentTime <= $storedEndTime) {
                // Kullanıcı süresindeyken tekrar başlatmış
                echo json_encode(['success' => true, 'message' => 'Kursa tekrar giriş yaptınız.']);
            } else {
                // Süresi bitmiş, kullanıcıya yeni kayıt oluştur
                $stmt = $db->prepare("UPDATE course_start_times SET start_time = :start_time, end_time = :end_time WHERE user_id = :user_id AND course_id = :course_id");
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
                $stmt->bindParam(':start_time', $currentTime, PDO::PARAM_STR);
                $stmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
                $stmt->execute();

                echo json_encode(['success' => true, 'message' => 'Süre sıfırlandı.']);
            }
        } else {
            // Kullanıcı bu kursu daha önce başlatmamış, yeni kayıt ekle
            $stmt = $db->prepare("
                INSERT INTO course_start_times (user_id, course_id, start_time, end_time)
                VALUES (:user_id, :course_id, :start_time, :end_time)
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
            $stmt->bindParam(':start_time', $currentTime, PDO::PARAM_STR);
            $stmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
            $stmt->execute();

            // Başarı yanıtı gönder
            echo json_encode(['success' => true]);
        }
    } catch (Exception $e) {
        // Hata yanıtı gönder
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // Geçersiz veri yanıtı gönder
    echo json_encode(['success' => false, 'message' => 'Geçersiz kullanıcı veya kurs ID.']);
}
?>
