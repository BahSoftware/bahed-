<?php
session_start();
include_once("inc/data.php"); // Veritabanı bağlantısı

$errorMessage = '';
$successMessage = '';

if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']);

    // Token'ı doğrulayın ve geçerliliğini kontrol edin
    $sql = "SELECT * FROM sifre_sifirlama WHERE token = :token AND expiry > NOW()";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resetRequest) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = htmlspecialchars($_POST['new_password']);
            $confirmPassword = htmlspecialchars($_POST['confirm_password']);

            if (strlen($newPassword) < 6) {
                $errorMessage = 'Şifre en az 6 karakter uzunluğunda olmalıdır.';
            } elseif ($newPassword !== $confirmPassword) {
                $errorMessage = 'Şifreler eşleşmiyor.';
            } else {
                // Şifreyi hash'leyin
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Kullanıcının ID'sini alın
                $userId = $resetRequest['user_id'];

                // Şifreyi güncelleyin
                $sql = "UPDATE kulanicilar SET sifre = :sifre WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':sifre', $hashedPassword);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();

                // Token'i veritabanından silin
                $sql = "DELETE FROM sifre_sifirlama WHERE token = :token";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':token', $token);
                $stmt->execute();

                $successMessage = 'Şifreniz başarıyla sıfırlandı. Giriş yapabilirsiniz.';

                // Yönlendirme
                header("Location: login.php");
                exit();
            }
        }
    } else {
        $errorMessage = 'Geçersiz veya süresi dolmuş şifre sıfırlama bağlantısı.';
    }
} else {
    $errorMessage = 'Geçersiz şifre sıfırlama bağlantısı.';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifreyi Yeniden Ayarla</title>
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
            <h3 class="text-center">Şifreyi Yeniden Ayarla</h3>
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
            <?php if (!empty($resetRequest)): ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . urlencode($token); ?>" method="post">
                    <div class="form-group">
                        <label for="new_password">Yeni Şifre:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Şifreyi Doğrula:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Şifreyi Yenile</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
