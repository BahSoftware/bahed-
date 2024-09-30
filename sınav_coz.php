<?php
include("inc/data.php");

$examId = $_GET['exam_id'] ?? '';

if (empty($examId)) {
    echo "Geçersiz sınav ID.";
    exit();
}

try {
    // Veritabanı bağlantısını kontrol et
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sınav bilgilerini ve soruları al
    $sql = "SELECT * FROM exams WHERE id = :exam_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['exam_id' => $examId]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exam) {
        echo "Sınav bulunamadı.";
        exit();
    }

    // Soruları al
    $sql = "SELECT * FROM questions WHERE exam_id = :exam_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['exam_id' => $examId]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Seçenekleri al
    $optionsSql = "SELECT * FROM options WHERE question_id IN (" . implode(',', array_column($questions, 'id')) . ")";
    $stmt = $db->prepare($optionsSql);
    $stmt->execute();
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Doğru cevapları al
    $correctOptions = [];
    $correctOptionsSql = "SELECT question_id, option_text FROM options WHERE is_correct = 1";
    $stmt = $db->prepare($correctOptionsSql);
    $stmt->execute();
    $correctOptionsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($correctOptionsData as $correctOption) {
        $correctOptions[$correctOption['question_id']] = $correctOption['option_text'];
    }

    // Seçenekleri sorularla ilişkilendir
    $questionsWithOptions = [];
    foreach ($questions as $question) {
        $questionsWithOptions[$question['id']] = $question;
        $questionsWithOptions[$question['id']]['options'] = [];
    }

    foreach ($options as $option) {
        $questionsWithOptions[$option['question_id']]['options'][] = $option;
    }

} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Çöz</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .question-container {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .question-number {
            font-weight: bold;
        }
        .points {
            font-weight: bold;
            color: #007bff;
        }
        .form-check {
            margin-bottom: 0.5rem;
        }
        .selected-option.correct {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .selected-option.incorrect {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .timer {
            font-size: 1.25rem;
            font-weight: bold;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4"><?php echo htmlspecialchars($exam['title']); ?></h3>
        <p><?php echo htmlspecialchars($exam['description']); ?></p>

        <div id="timer" class="timer mb-4">15:00</div> <!-- Geri sayım için alan -->

        <form action="sınav_tamamlandi.php" method="post">
            <input type="hidden" name="exam_id" value="<?php echo htmlspecialchars($examId); ?>">

            <?php $questionNumber = 1; ?>
            <?php foreach ($questionsWithOptions as $questionId => $question): ?>
                <div class="question-container">
                    <div class="question-header">
                        <div class="question-number">Soru <?php echo $questionNumber; ?>:</div>
                        <div class="points"><?php echo htmlspecialchars($question['points']); ?> puan</div>
                    </div>
                    <div><?php echo htmlspecialchars($question['question_text']); ?></div>
                    <input type="hidden" name="questions[<?php echo $questionId; ?>][id]" value="<?php echo htmlspecialchars($questionId); ?>">
                    <?php foreach ($question['options'] as $option): ?>
                        <div class="form-check" data-question-id="<?php echo $questionId; ?>">
                            <input class="form-check-input" type="radio" name="questions[<?php echo $questionId; ?>][option]" value="<?php echo htmlspecialchars($option['id']); ?>" id="option-<?php echo $option['id']; ?>" data-option-text="<?php echo htmlspecialchars($option['option_text']); ?>">
                            <label class="form-check-label" for="option-<?php echo $option['id']; ?>">
                                <?php echo htmlspecialchars($option['option_text']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php $questionNumber++; ?>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Sınavı Tamamla</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
   document.addEventListener('DOMContentLoaded', function() {
    // PHP'den sınav ID'sini al
    var examId = '<?php echo htmlspecialchars($examId, ENT_QUOTES, 'UTF-8'); ?>';
    var storageKey = 'exam_timer_' + examId; // Sınav ID'si ile birlikte anahtar oluştur

    var duration = localStorage.getItem(storageKey);

    // Eğer süre yoksa, yeni bir süre başlatın (15 dakika)
    if (duration === null) {
        duration = 15 * 60; // 15 dakika
    } else {
        duration = parseInt(duration, 10);
    }

    var display = document.querySelector('#timer');

    function updateTimer() {
        var minutes = Math.floor(duration / 60);
        var seconds = duration % 60;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        display.textContent = minutes + ':' + seconds;

        if (duration <= 0) {
            clearInterval(timerInterval);
            alert('Süreniz doldu! Sınavınız tamamlanıyor.');
            document.querySelector('form').submit(); // Formu otomatik olarak gönder
        } else {
            duration--;
            localStorage.setItem(storageKey, duration); // Süreyi localStorage'a kaydet
        }
    }

    // Süreyi güncelleyen intervali başlat
    var timerInterval = setInterval(updateTimer, 1000);

    // Formu submit etmeden önce geri sayımı durdurma
    document.querySelector('form').addEventListener('submit', function() {
        clearInterval(timerInterval);
        localStorage.removeItem(storageKey); // Form submit edildikten sonra süreyi temizle
    });

    // Sayfa yüklenirken kalan süreyi göster
    updateTimer();
});

</script>

</body>
</html>


