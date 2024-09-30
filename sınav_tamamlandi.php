<?php
include("inc/header.php");

// Varsayılan olarak $showResult'ı false olarak ayarlayın
$showResult = false;

$userId = $_SESSION['user_id']; 
$examId = $_POST['exam_id'] ?? '';
$answers = $_POST['questions'] ?? '';

// İşlem zaten yapıldı mı kontrol et
if (isset($_SESSION['exam_completed']) && $_SESSION['exam_completed'] == $examId) {
    $message = "Sınav sonucu zaten kaydedildi.";
} else {
    if (empty($examId)) {
        echo "Geçersiz sınav ID.";
        exit();
    }

    try {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Sınavın geçerli olup olmadığını kontrol et ve sınav başlığını al
        $examCheckSql = "SELECT id, title FROM exams WHERE id = :exam_id";
        $stmt = $db->prepare($examCheckSql);
        $stmt->execute(['exam_id' => $examId]);
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exam) {
            echo "Geçersiz sınav ID.";
            exit();
        }

        // Sınav başlığını al
        $examTitle = htmlspecialchars($exam['title']);

        // Doğru cevapları al
        $correctOptions = [];
        $correctOptionsSql = "SELECT question_id, option_text FROM options WHERE is_correct = 1";
        $stmt = $db->prepare($correctOptionsSql);
        $stmt->execute();
        $correctOptionsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($correctOptionsData as $correctOption) {
            $correctOptions[$correctOption['question_id']] = $correctOption['option_text'];
        }

        // Puan hesaplama
        $totalPoints = 0;
        $pointsPerQuestion = [];
        $questionsSql = "SELECT id, points FROM questions WHERE exam_id = :exam_id";
        $stmt = $db->prepare($questionsSql);
        $stmt->execute(['exam_id' => $examId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questions as $question) {
            $pointsPerQuestion[$question['id']] = $question['points'];
        }

        foreach ($answers as $questionId => $answer) {
            $selectedOptionId = $answer['option'] ?? '';
            if ($selectedOptionId) {
                // Seçilen cevabın doğru olup olmadığını kontrol et
                $selectedOptionSql = "SELECT option_text FROM options WHERE id = :option_id";
                $stmt = $db->prepare($selectedOptionSql);
                $stmt->execute(['option_id' => $selectedOptionId]);
                $selectedOption = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($selectedOption && isset($correctOptions[$questionId]) && $selectedOption['option_text'] === $correctOptions[$questionId]) {
                    $totalPoints += $pointsPerQuestion[$questionId];
                }
            }
        }

        // Kullanıcı adını al
        $userSql = "SELECT kullanici_ad FROM kulanicilar WHERE id = :user_id";
        $stmt = $db->prepare($userSql);
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $username = htmlspecialchars($user['kullanici_ad']);
        } else {
            $username = "Bilinmiyor";
        }

        // Sonuçları veritabanına kaydet
        $insertSql = "INSERT INTO exam_results (exam_id, exam_title, user_id, username, score) VALUES (:exam_id, :exam_title, :user_id, :username, :score)";
        $stmt = $db->prepare($insertSql);
        $stmt->execute([
            'exam_id' => $examId,
            'exam_title' => $examTitle,
            'user_id' => $userId,
            'username' => $username,
            'score' => $totalPoints
        ]);

        // İşlem tamamlandığını işaretle
        $_SESSION['exam_completed'] = $examId;

        // Mesajı ayarla
        $message = "Sonuçlarınız başarıyla kaydedildi.";
        $showResult = true; // Sonuçları göstermek için true yapın

    } catch (PDOException $e) {
        $message = "Veritabanı hatası: " . htmlspecialchars($e->getMessage());
        $showResult = false;
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Sonucu</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .header-menu {
            padding: 10px 0;
            background-color: #007bff;
            color: white;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .result-container {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
            width: 100%;
            max-width: 600px;
        }
        .result-container h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .result-container p {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .alert-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 0.5rem;
            padding: 1rem;
            color: #155724;
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
        }
        .alert-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-primary-custom {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        .btn-primary-custom:hover {
            background-color: #0056b3;
            border-color: #004080;
        }
    </style>
</head>
<body>
    <div class="header-menu">
        <a href="kurslarım" class="btn btn-primary-custom">Ana Sayfaya Dön</a>
    </div>
    <div class="container">
        <div class="result-container">
            <?php if (isset($message)): ?>
                <div class="alert-box">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($showResult): ?>
                <h1>Sınav Sonucu</h1>
                <p><strong>Sınav:</strong> <?php echo htmlspecialchars($examTitle); ?></p>
                <p><strong>Kullanıcı Adı:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Toplam Puan:</strong> <?php echo htmlspecialchars($totalPoints); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
