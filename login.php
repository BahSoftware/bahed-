<?php
include("inc/data.php"); // Veritabanı bağlantısı
require 'vendor/autoload.php'; // PHPMailer'ı yükleyin

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start(); // Oturum başlat

$errorMessage = '';

function sendVerificationCode($email, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'mail.buyukanadolu.com.tr';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'eren.ozkan@buyukanadolu.com.tr';
        $mail->Password   = 'E+123456e344149';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('info@buyukanadolu.com.tr', 'Bah Egitim');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Dogrulama Kodunuz';
        $mail->Body    = 'Dogrulama kodunuz: <b>' . $code . '</b>';
        $mail->AltBody = 'Dogrulama kodunuz: ' . $code;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Hata mesajını kaydedin (loglama veya hata raporlama)
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $usernameOrEmail = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        // Kullanıcıyı veritabanından çek
        $sql = "SELECT id, sifre, email FROM kulanicilar WHERE kullanici_ad = :usernameOrEmail OR email = :usernameOrEmail";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':usernameOrEmail', $usernameOrEmail, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['sifre'])) {
            $userId = $user['id'];
            $userEmail = $user['email'];

            // Doğrulama kodunu oluştur
            $verificationCode = rand(100000, 999999);
            $expiryTime = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Doğrulama kodunu veritabanına kaydet
            $insertCode = $db->prepare("INSERT INTO verify_code (user_id, code, expiry_time) VALUES (:user_id, :code, :expiry_time)");
            $insertCode->execute([
                'user_id' => $userId,
                'code' => $verificationCode,
                'expiry_time' => $expiryTime
            ]);

            // E-posta gönder
            if (sendVerificationCode($userEmail, $verificationCode)) {
                $_SESSION['user_id'] = $userId;
                header("Location: verify_code.php");
                exit();
            } else {
                $errorMessage = 'Doğrulama kodu gönderilirken bir hata oluştu.';
            }
        } else {
            $errorMessage = 'Kullanıcı adı veya şifre hatalı.';
        }
    } catch(PDOException $e) {
        $errorMessage = 'Veritabanı hatası: ' . $e->getMessage();
    }
    
    // Bağlantıyı kapat
    $db = null;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .login-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .login-card .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h3 class="text-center">Giriş Yap</h3>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı veya E-posta:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Şifre:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Giriş Yap</button>
            </form>
            <div class="text-center mt-3">
                <a href="register.php">Kayıt Ol</a>
            </div>
            <div class="text-center mt-3">
                <a href="password_reset.php">Şifremi Unuttum?</a>
            </div>
        </div>
    </div>
</body>
</html>
