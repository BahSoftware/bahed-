<?php
session_start();
include_once("inc/data.php"); // Veritabanı bağlantısı

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Composer autoload
require 'vendor/autoload.php';

$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Geçerli bir e-posta adresi girin.';
    } else {
        try {
            // E-posta adresi ile kullanıcıyı bul
            $sql = "SELECT id FROM kulanicilar WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $resetToken = bin2hex(random_bytes(16)); // Şifre sıfırlama token'ı oluştur
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token'in geçerlilik süresi

                // Token'i veritabanına kaydet
                $sql = "INSERT INTO sifre_sifirlama (user_id, token, expiry) VALUES (:user_id, :token, :expiry)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':user_id', $user['id']);
                $stmt->bindParam(':token', $resetToken);
                $stmt->bindParam(':expiry', $expiry);
                $stmt->execute();

                // PHPMailer kullanarak e-posta gönder
                $mail = new PHPMailer(true);
                try {
                    // Sunucu ayarları
                    $mail->isSMTP();                                      // SMTP kullan
                    $mail->Host       = 'mail.buyukanadolu.com.tr';           // SMTP sunucu adresi
                    $mail->SMTPAuth   = true;                             // SMTP kimlik doğrulaması
                    $mail->Username   = 'eren.ozkan@buyukanadolu.com.tr';     // SMTP kullanıcı adı
                    $mail->Password   = 'E+123456e344149';           // SMTP şifresi
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    // TLS kullan
                    $mail->Port       = 587;                              // TCP portu

                    // Alıcı
                    $mail->setFrom('info@buyukanadolu.com.tr', 'Bah Egitim');
                    $mail->addAddress($email);

                    // İçerik
                    $mail->isHTML(true);                                  // HTML formatında e-posta
                    $mail->Subject = 'Sifre Sifirlama Talebi';
                    $mail->Body    = 'Şifrenizi sıfırlamak için <a href="http://localhost/bahed%C3%BC/reset_password.php?token=' . $resetToken . '">bu bağlantıya</a> tıklayın.';
                    $mail->AltBody = 'Şifrenizi sıfırlamak için şu bağlantıya tıklayın: http://localhost/bahed%C3%BC/reset_password.php?token=' . $resetToken;

                    $mail->send();
                    $successMessage = 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.';
                } catch (Exception $e) {
                    $errorMessage = "E-posta gönderilemedi. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $errorMessage = 'E-posta adresiniz sistemimizde kayıtlı değil.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Hata: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .reset-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .reset-card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .reset-card .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <h3 class="text-center">Şifre Sıfırlama</h3>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email">E-posta:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">Şifre Sıfırlama Bağlantısını Gönder</button>
            </form>
        </div>
    </div>
</body>
</html>
