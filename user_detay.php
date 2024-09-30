<?php
include_once("inc/data.php"); // PDO bağlantısını içerir

$successMessage = '';
$errorMessage = '';

// Kullanıcı ID'sini al
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $errorMessage = 'Geçersiz veya eksik kullanıcı ID\'si.';
} else {
    $userId = intval($_GET['id']);
}

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Form verilerini al
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $passwordRepeat = htmlspecialchars($_POST['password_repeat']);
        $hastane = htmlspecialchars($_POST['hastane']);
        $telefon = htmlspecialchars($_POST['telefon_no']);
        $bolum = htmlspecialchars($_POST['bolum']);
        $userRole = intval($_POST['user_role']);
        $aktif = isset($_POST['online']) ? 1 : 0;

        // Şifreleri karşılaştır
        if ($password !== $passwordRepeat) {
            $errorMessage = 'Şifreler eşleşmiyor.';
        } else {
            // Şifreyi hashle (eğer şifre girilmişse güncelle)
            $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null;

            // Veriyi veritabanına güncelle
            $sql = "UPDATE kulanicilar SET 
                        kullanici_ad = :username, 
                        email = :email, 
                        hastane = :hastane, 
                        telefon_no = :telefon_no, 
                        bolum = :bolum, 
                        user_rol = :user_role, 
                        online = :online" . 
                   ($hashedPassword ? ", sifre = :password" : "") . 
                   " WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':hastane', $hastane);
            $stmt->bindParam(':telefon_no', $telefon);
            $stmt->bindParam(':bolum', $bolum);
            $stmt->bindParam(':user_role', $userRole);
            $stmt->bindParam(':online', $aktif);
            if ($hashedPassword) {
                $stmt->bindParam(':password', $hashedPassword);
            }
            $stmt->bindParam(':id', $userId);

            $stmt->execute();
            
            // Başarılı mesajı
            $successMessage = 'Kullanıcı başarıyla güncellendi!';
        }
    } catch(PDOException $e) {
        // Hata mesajı
        $errorMessage = 'Hata: ' . $e->getMessage();
    }
}

// Kullanıcı bilgilerini veritabanından al
try {
    $sql = "SELECT * FROM kulanicilar WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errorMessage = 'Kullanıcı bulunamadı.';
    }
} catch(PDOException $e) {
    $errorMessage = 'Hata: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Düzenle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        .container {
            max-width: 800px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Kullanıcı Düzenleme Formu</h2>

        <?php if (!empty($successMessage)): ?>
            <script>
                swal({
                    title: 'Başarılı!',
                    text: '<?php echo $successMessage; ?>',
                    icon: 'success',
                    button: 'Tamam'
                }).then(function() {
                    window.location.href = 'user.php'; // Yönlendirme yapabilirsiniz
                });
            </script>
        <?php elseif (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($user)): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $userId; ?>" method="post">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['kullanici_ad']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-posta:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Şifre:</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <input type="checkbox" id="show-password"> Şifreyi Göster
                </div>
                <div class="form-group">
                    <label for="password_repeat">Şifre Tekrar:</label>
                    <input type="password" class="form-control" id="password_repeat" name="password_repeat">
                </div>
                <div class="form-group">
                    <label for="hastane">Hastane:</label>
                    <input type="text" class="form-control" id="hastane" name="hastane" value="<?php echo htmlspecialchars($user['hastane']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefon">Telefon Numarası:</label>
                    <input type="tel" class="form-control" id="telefon_no" name="telefon_no" value="<?php echo htmlspecialchars($user['telefon_no']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="bolum">Bölüm:</label>
                    <input type="text" class="form-control" id="bolum" name="bolum" value="<?php echo htmlspecialchars($user['bolum']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="user_role">Yetki:</label>
                    <select class="form-control" id="user_role" name="user_role" required>
                        <option value="1" <?php if ($user['user_rol'] == 1) echo 'selected'; ?>>Yönetici</option>
                        <option value="2" <?php if ($user['user_rol'] == 2) echo 'selected'; ?>>Personel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="aktif">Aktif:</label>
                    <input type="checkbox" id="online" name="online" <?php echo $user['online'] ? 'checked' : ''; ?>>
                </div>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            var passwordField = document.getElementById('password');
            if (this.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        });
    </script>
</body>
</html>
<br><br>
