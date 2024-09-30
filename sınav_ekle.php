<?php
include("inc/data.php");

// Form gönderildiyse verileri işleme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sınav başlığı ve açıklaması
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    // Sorular dizisi var mı kontrol et
    $questions = $_POST['questions'] ?? [];

    try {
        // Veritabanı bağlantısını kontrol et
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Sınavı veritabanına ekle
        $sql = "INSERT INTO exams (title, description) VALUES (:title, :description)";
        $stmt = $db->prepare($sql);
        $stmt->execute(['title' => $title, 'description' => $description]);
        $examId = $db->lastInsertId();

        // Soruları ve seçenekleri ekle
        foreach ($questions as $questionId => $question) {
            $questionText = $question['text'] ?? '';
            $points = intval($question['points']); // Puanı al

            // Sorunun boş olup olmadığını kontrol et
            if (empty($questionText)) {
                continue; // veya hata mesajı ver
            }

            $sql = "INSERT INTO questions (exam_id, question_text, points) VALUES (:exam_id, :question_text, :points)";
            $stmt = $db->prepare($sql);
            $stmt->execute(['exam_id' => $examId, 'question_text' => $questionText, 'points' => $points]);
            $newQuestionId = $db->lastInsertId();

            foreach ($question['options'] as $index => $option) {
                $optionText = $option['text'] ?? '';
                $isCorrect = isset($option['correct']) ? 1 : 0;

                // Seçeneğin boş olup olmadığını kontrol et
                if (empty($optionText)) {
                    continue; // veya hata mesajı ver
                }

                $sql = "INSERT INTO options (question_id, option_text, is_correct) VALUES (:question_id, :option_text, :is_correct)";
                $stmt = $db->prepare($sql);
                $stmt->execute(['question_id' => $newQuestionId, 'option_text' => $optionText, 'is_correct' => $isCorrect]);
            }
        }

         // Başarılı işlem sonrası sınav bağlantısını göster
         $link = "http://localhost/bahed%C3%BC/s%C4%B1nav_coz.php?exam_id=" . $examId;
         echo "<div class='container mt-5'>";
         echo "<div class='alert alert-success' role='alert'>";
         echo "<h4 class='alert-heading'>Sınav başarıyla oluşturuldu!</h4>";
         echo "<p>Sınavı çözmek için aşağıdaki bağlantıyı kullanabilirsiniz Veya Kopyalayabilirsiniz:</p>";
         echo "<p><a href='$link' class='alert-link'>$link</a></p>";
         echo "<p><a href='sınav.php' class='alert-link'>Ana Sayfaya Dön</a></p>";
         echo "</div>";
         echo "</div>";

    } catch (PDOException $e) {
        // Hata mesajını URL parametresi olarak geç
        header("Location: sınav.php?status=error&message=" . urlencode($e->getMessage()));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Oluştur</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Sınav Oluştur</h1>
        <form id="examForm" action="" method="post">
            <div class="form-group">
                <label for="title">Sınav Başlığı:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama:</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <div id="questions-container">
                <!-- Sorular dinamik olarak buraya eklenecek -->
            </div>

            <button type="button" class="btn btn-secondary" id="add-question">Soru Ekle</button>
            <button type="submit" class="btn btn-primary">Sınavı Kaydet</button>
        </form>
    </div>

    <script>
        let questionCount = 0;

        document.getElementById('add-question').addEventListener('click', () => {
            questionCount++;
            const container = document.getElementById('questions-container');

            // Sorular için HTML ekle
            const questionHtml = `
                <div class="mt-4" id="question-${questionCount}">
                    <h4>Soru ${questionCount}</h4>
                    <button type="button" class="btn btn-danger btn-sm float-right" onclick="removeQuestion(${questionCount})">Soru Sil</button>
                    <div class="form-group">
                        <label for="question${questionCount}">Soru:</label>
                        <input type="text" class="form-control" id="question${questionCount}" name="questions[${questionCount}][text]" required>
                    </div>
                    <div class="form-group">
                        <label for="points${questionCount}">Puan:</label>
                        <input type="number" class="form-control" id="points${questionCount}" name="questions[${questionCount}][points]" value="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Seçenekler:</label>
                        <div id="options-${questionCount}">
                            <div class="form-group">
                                <input type="text" class="form-control" name="questions[${questionCount}][options][0][text]" required>
                                <input type="checkbox" name="questions[${questionCount}][options][0][correct]"> Doğru
                                <button type="button" class="btn btn-danger btn-sm float-right" onclick="removeOption(${questionCount}, 0)">Seçeneği Sil</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="addOption(${questionCount})">Seçenek Ekle</button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', questionHtml);
        });

        function addOption(questionId) {
            const optionsContainer = document.getElementById(`options-${questionId}`);
            const optionCount = optionsContainer.querySelectorAll('.form-group').length;
            const optionHtml = `
                <div class="form-group">
                    <input type="text" class="form-control" name="questions[${questionId}][options][${optionCount}][text]" required>
                    <input type="checkbox" name="questions[${questionId}][options][${optionCount}][correct]"> Doğru
                    <button type="button" class="btn btn-danger btn-sm float-right" onclick="removeOption(${questionId}, ${optionCount})">Seçeneği Sil</button>
                </div>
            `;
            optionsContainer.insertAdjacentHTML('beforeend', optionHtml);
        }

        function removeQuestion(questionId) {
            const questionElement = document.getElementById(`question-${questionId}`);
            questionElement.remove();
        }

        function removeOption(questionId, optionIndex) {
            const optionsContainer = document.getElementById(`options-${questionId}`);
            const optionElements = optionsContainer.querySelectorAll('.form-group');
            if (optionElements.length > 1) {
                optionElements[optionIndex].remove();
                // Reindex options
                optionElements.forEach((element, index) => {
                    element.querySelector('input[type="text"]').name = `questions[${questionId}][options][${index}][text]`;
                    element.querySelector('input[type="checkbox"]').name = `questions[${questionId}][options][${index}][correct]`;
                });
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
