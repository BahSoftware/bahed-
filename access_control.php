<?php
include_once("inc/header.php");

// Kullanıcı rolüne göre kısıtlama koyma işlemi
if ($userRole === '1') {
    // Admin kullanıcı için özel işlemler
} elseif ($userRole === '2') {
    // Yetkisi olmayan kullanıcılar için SweetAlert2 ile mesaj gösterme ve yönlendirme
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Yetki Kısıtlaması</title>
        <!-- SweetAlert2 CSS -->
        <link href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css' rel='stylesheet'>
    </head>
    <body>
        <!-- SweetAlert2 JavaScript -->
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js'></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Yetkiniz Yok',
                text: 'Bu sayfaya erişmek için yetkiniz yok.',
                confirmButtonText: 'Tamam',
                timer: 3000, // 3 saniye içinde otomatik kapanır
                willClose: () => {
                    window.location.href = 'http://localhost/bahedü/anasayfa';
                }
            });
        </script>
    </body>
    </html>";
    exit(); // Yönlendirmeden sonra kodun devam etmesini engeller
} else {
    // Diğer kullanıcı türleri için işlemler
}
?>
