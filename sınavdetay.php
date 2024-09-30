<?php
include("inc/data.php");
// Sınav ID'sini al
$examId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Varsayılan mesaj
$alert = '';
$alertType = '';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['status'])) {
    $status = $_GET['status'];
    $message = $_GET['message'] ?? '';

    if ($status === 'success') {
        $alert = "Sınav başarıyla kaydedildi.";
        $alertType = 'success';
    } elseif ($status === 'error') {
        $alert = "Bir hata oluştu: " . htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $alertType = 'error';
    }
}

try {
    // Veritabanı bağlantısını kontrol et
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sınavı ve soruları veritabanından çek
    $sql = "SELECT * FROM exams WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $examId]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($exam) {
        $sql = "SELECT * FROM questions WHERE exam_id = :exam_id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['exam_id' => $examId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questions as &$question) {
            $sql = "SELECT * FROM options WHERE question_id = :question_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['question_id' => $question['id']]);
            $question['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        // Sınav bulunamazsa hata mesajı
        $alert = "Sınav bulunamadı.";
        $alertType = 'error';
    }

} catch (PDOException $e) {
    $alert = "Bir hata oluştu: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $alertType = 'error';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Detayı</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .correct-option {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .no-questions {
            font-style: italic;
            color: #6c757d;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Sınav Detayı</h1>

        <?php if ($alert): ?>
            <script>
                Swal.fire({
                    title: '<?php echo $alertType === 'success' ? 'Başarılı!' : 'Hata!'; ?>',
                    text: '<?php echo $alert; ?>',
                    icon: '<?php echo $alertType; ?>',
                    confirmButtonText: 'Tamam'
                });
            </script>
        <?php endif; ?>

        <?php if ($exam): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($exam['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($exam['description'], ENT_QUOTES, 'UTF-8')); ?></p>

                    <?php if ($questions): ?>
                        <h5 class="mt-4">Sorular</h5>
                        <ul class="list-group">
                            <?php foreach ($questions as $question): ?>
                                <li class="list-group-item">
                                    <h6><?php echo htmlspecialchars($question['question_text'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                    <ul class="list-group">
                                        <?php foreach ($question['options'] as $option): ?>
                                            <li class="list-group-item<?php echo $option['is_correct'] ? ' correct-option' : ''; ?>">
                                                <?php echo htmlspecialchars($option['option_text'], ENT_QUOTES, 'UTF-8'); ?>
                                                <?php if ($option['is_correct']): ?>
                                                    <span class="badge badge-success float-right">Doğru</span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-questions">Bu sınavda soru bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <p>Sınav bilgisi bulunamadı.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
