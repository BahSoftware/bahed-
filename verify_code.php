<?php
session_start();
include_once("inc/data.php"); // Veritabanı bağlantısı

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verificationCode = htmlspecialchars($_POST['verification_code']);

    try {
        // Kullanıcının doğrulama kodunu al
        $sql = "SELECT * FROM verify_code WHERE user_id = :user_id AND code = :verification_code AND expiry_time > NOW()";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':verification_code', $verificationCode);
        $stmt->execute();
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($verification) {
            // Doğrulama başarılı, kodu temizle ve kullanıcıyı yönlendir
            $updateSql = "DELETE FROM verify_code WHERE user_id = :user_id";
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->bindParam(':user_id', $_SESSION['user_id']);
            $updateStmt->execute();

            header("Location: index.php"); // Başarılı doğrulama sonrası yönlendir
            exit();
        } else {
            $errorMessage = 'Doğrulama kodu hatalı veya süresi dolmuş.';
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
    <title>Doğrulama Kodu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .verify-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .verify-card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .verify-card .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <h3 class="text-center">Doğrulama Kodu</h3>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="verification_code">(Mail Adresini Kontrol Et)Doğrulama Kodu:</label>
                    <input type="text" class="form-control" id="verification_code" name="verification_code" required>
                </div>
                <button type="submit" class="btn btn-primary">Doğrula</button>
            </form>
        </div>
    </div>
</body>
</html>
