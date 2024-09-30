<?php
session_start(); // Oturumu başlat

// Oturumu sonlandır
session_unset();  // Tüm oturum değişkenlerini temizler
session_destroy(); // Oturumu yok eder

// Kullanıcıyı login sayfasına yönlendir
header("Location: login.php");
exit(); // Yönlendirme sonrası scriptin çalışmasını durdurur
?>
