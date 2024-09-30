<?php
session_start();
include_once("inc/data.php"); // Veritabanı bağlantısını içerir

$errorMessage = '';
$departmanlar = [];
$hastaneler = [];

// Departmanları çekme
try {
    $departmanSql = "SELECT Departman_adi FROM departman";
    $departmanStmt = $db->prepare($departmanSql);
    $departmanStmt->execute();
    $departmanlar = $departmanStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Hata: ' . $e->getMessage();
}

// Hastaneleri çekme
try {
    $hastaneSql = "SELECT hastane_adi FROM hastane";
    $hastaneStmt = $db->prepare($hastaneSql);
    $hastaneStmt->execute();
    $hastaneler = $hastaneStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Hata: ' . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $telefonNo = htmlspecialchars($_POST['telefon_no']);
    $bolum = htmlspecialchars($_POST['bolum']);
    $hastaneAdi = htmlspecialchars($_POST['hastane']); // Hastane adı
    $departmanAdi = htmlspecialchars($_POST['departman']); // Departman adı

    // Şifreyi hashleme
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Kullanıcı adı veya e-posta ile kontrol et
        $sql = "SELECT * FROM kulanicilar WHERE kullanici_ad = :username OR email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $errorMessage = 'Bu kullanıcı adı veya e-posta zaten kayıtlı.';
        } else {
            // Hastane ve Departman adlarının doğruluğunu kontrol et
            $isHastaneValid = in_array($hastaneAdi, array_column($hastaneler, 'hastane_adi'));
            $isDepartmanValid = in_array($departmanAdi, array_column($departmanlar, 'Departman_adi'));

            if (!$isHastaneValid) {
                $errorMessage = 'Geçersiz hastane adı.';
            } elseif (!$isDepartmanValid) {
                $errorMessage = 'Geçersiz departman adı.';
            } else {
                // Yeni kullanıcıyı ekle
                $sql = "INSERT INTO kulanicilar (kullanici_ad, email, sifre, hastane, telefon_no, bolum, departman) 
                        VALUES (:username, :email, :password, :hastane, :telefon_no, :bolum, :departman)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':hastane', $hastaneAdi);
                $stmt->bindParam(':telefon_no', $telefonNo);
                $stmt->bindParam(':bolum', $bolum);
                $stmt->bindParam(':departman', $departmanAdi);
                $stmt->execute();

                $_SESSION['user_id'] = $db->lastInsertId();
                $_SESSION['user_role'] = 'user'; // Varsayılan rol
                header("Location: login.php"); // Başarılı kayıt sonrası yönlendir
                exit();
            }
        }
    } catch (PDOException $e) {
        $errorMessage = 'Hata: ' . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-card {
            max-width: 500px;
            width: 100%;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .form-card h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            text-align: center;
            color: #343a40;
        }
        .form-group label {
            font-weight: bold;
            color: #495057;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <h2>Kayıt Ol</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">E-posta:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Şifre:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="hastane">Hastane:</label>
                    <select class="form-control" id="hastane" name="hastane" required>
                        <?php foreach ($hastaneler as $hastane): ?>
                            <option value="<?php echo htmlspecialchars($hastane['hastane_adi']); ?>">
                                <?php echo htmlspecialchars($hastane['hastane_adi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="telefon_no">Telefon Numarası:</label>
                    <input type="tel" class="form-control" id="telefon_no" name="telefon_no" required>
                </div>
                <div class="form-group">
                    <label for="departman">Departman:</label>
                    <select class="form-control" id="departman" name="departman" required>
                        <?php foreach ($departmanlar as $departman): ?>
                            <option value="<?php echo htmlspecialchars($departman['Departman_adi']); ?>">
                                <?php echo htmlspecialchars($departman['Departman_adi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bolum">Bölüm:</label>
                    <input type="text" class="form-control" id="bolum" name="bolum" required>
                </div>
                <button type="submit" class="btn btn-primary">Kayıt Ol</button>
            </form>
        </div>
    </div>
</body>

</html><br><br>
