<?php
session_start();
include_once("inc/data.php"); // Veritabanı bağlantısı

if (isset($_SESSION['user_id'])) {
    try {
        $userId = $_SESSION['user_id'];
        $sql = "UPDATE kulanicilar SET online = 1 WHERE id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    } catch (PDOException $e) {
        // Hata işleme
    }
}
?>
