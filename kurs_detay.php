<!-- Include SweetAlert2 CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<?php
include("inc/header.php");

// Kullanıcı ID'sini oturumdan al
$userId = $_SESSION['user_id']; 
$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo "<div class='alert alert-danger'>Geçersiz ID.</div>";
    exit;
}

$parts = explode('/', $id);
$haber_id = $parts[0];

// Veritabanından haber detaylarını al
$sql = $db->prepare("SELECT * FROM bahedu WHERE id = :id");
$sql->bindParam(':id', $haber_id);
$sql->execute();
$haber = $sql->fetch(PDO::FETCH_ASSOC);

if (!$haber) {
    echo "<div class='alert alert-danger'>Haber bulunamadı.</div>";
    exit;
}

// Kullanıcının bu eğitimi tamamlayıp tamamlamadığını kontrol et
$progressCheckSql = "SELECT completed FROM user_progress WHERE user_id = :user_id AND haber_id = :haber_id";
$stmt = $db->prepare($progressCheckSql);
$stmt->execute([
    'user_id' => $userId,
    'haber_id' => $haber_id
]);
$progress = $stmt->fetch(PDO::FETCH_ASSOC);

// Eğitim tamamlandıysa
if ($progress && $progress['completed']) {
    echo "<div class='alert alert-warning' style='text-align:center; top:300px;'>Bu eğitime zaten katıldınız ve tamamladınız.</div>";
    echo "<div class='alert alert-success' style='text-align:center; top:300px;'><a href='anasayfa'>Ana Sayfaya Dön</a></div>";
    exit;
}

// Eğitim tamamlandığında veritabanına kaydet
function markAsCompleted($db, $userId, $haber_id) {
    $insertSql = "INSERT INTO user_progress (user_id, haber_id, completed) VALUES (:user_id, :haber_id, TRUE)
                  ON DUPLICATE KEY UPDATE completed = TRUE";
    $stmt = $db->prepare($insertSql);
    $stmt->execute([
        'user_id' => $userId,
        'haber_id' => $haber_id
    ]);
}
?>

<style>
    .hero-section {
        background-image: linear-gradient(to right, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0) 100%), url('img/carousel-2.jpg');
        background-size: cover;
        background-position: center;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: 0 20px;
        border-bottom: 5px solid #007bff;
        position: relative;
        overflow: hidden;
    }

    .hero-section h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 0;
        text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7);
        color: white;
        max-width: 90%;
        line-height: 1.2;
    }
</style>

<div class="hero-section">
    <h1><?php echo htmlspecialchars($haber['baslik']); ?></h1>
</div>


<div class="container mt-5">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                <div id="timer" style="font-size: 1.2rem; color: red; font-weight: bold;"></div>
                    <h5 class="card-title"><?php echo htmlspecialchars($haber['baslik']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($haber['aciklama']); ?></p>

                    <!-- İçerik gösterim bölümü -->
                    <?php if ($haber['on_test']): ?>
                        <h6>Ön Test:</h6>
                        <a id="testButton" href="<?php echo htmlspecialchars($haber['on_test_url']); ?>" target="_blank" class="btn btn-warning">Ön Testi Başlat</a>
                        <p id="testMessage" style="display: none; color: green;">Ön test yapıldıktan sonra içerik aktif olacak.</p>
                    <?php endif; ?>

                    <h6>Video:</h6>
                    <div id="videoContainer" style="display: none;">
                        <video id="videoPlayer" width="100%" controls style="display: none;">
                            <source src="<?php echo htmlspecialchars($haber['video']); ?>" type="video/mp4">
                            Tarayıcınız video etiketini desteklemiyor.
                        </video>
                    </div>
                    <button id="playButton" class="btn btn-primary" style="display: none;">Oynat</button>
                    <button id="sinaviGecBtn" class="btn btn-success" disabled style="display: none;">Sınavı Geç</button>
                    <p id="message" style="display: none; color: green;">Sınavı geçtiniz ve eğitim tamamlandı!</p>
                    <p id="reloadMessage" style="display: none; color: red;">Sayfayı yenilerseniz başa dönersiniz!</p>

                    <?php if (!empty($haber['pdf'])): ?>
                        <h6>PDF Dosyası:</h6>
                        <a href="<?php echo htmlspecialchars($haber['pdf']); ?>" target="_blank" class="btn btn-secondary">PDF'i Görüntüle</a>
                    <?php endif; ?>
                    
                    <p><small>Sınav Tarihi: <?php echo htmlspecialchars($haber['created_at']); ?></small></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Video ID'sini PHP'den al
    const videoId = "<?php echo htmlspecialchars($haber['id']); ?>";
    const videoTimeKey = `videoTime_${videoId}`;
    const videoCompletedKey = `videoCompleted_${videoId}`;
    const testButton = document.getElementById('testButton');
    const testMessage = document.getElementById('testMessage');
    const playButton = document.getElementById('playButton');
    const videoContainer = document.getElementById('videoContainer');
    const video = document.getElementById('videoPlayer');
    const sinaviGecBtn = document.getElementById('sinaviGecBtn');
    const message = document.getElementById('message');
    const reloadMessage = document.getElementById('reloadMessage');
    const sinavUrl = "<?php echo htmlspecialchars($haber['sinav_url']); ?>";
    const timerDisplay = document.getElementById('timer');

    // 40 dakikalık zamanlayıcıyı tanımla
    const countdownTime = 40 * 60 * 1000; // 40 dakika
    const startTimeKey = `startTime_${videoId}`;

    function getRemainingTime() {
        const startTime = parseInt(localStorage.getItem(startTimeKey), 10);
        const now = Date.now();
        const remainingTime = (startTime + countdownTime) - now;
        return remainingTime > 0 ? remainingTime : 0;
    }

    function formatTime(ms) {
        const minutes = Math.floor(ms / (1000 * 60));
        const seconds = Math.floor((ms % (1000 * 60)) / 1000);
        return `${minutes} dakika ${seconds} saniye`;
    }

    function updateTimer() {
        const remainingTime = getRemainingTime();
        if (remainingTime <= 0) {
            markAsFailed();
            timerDisplay.textContent = 'Süre doldu!';
        } else {
            timerDisplay.textContent = `Kalan süre: ${formatTime(remainingTime)}`;
        }
    }

    function markAsFailed() {
        // SweetAlert2 ile uyarı göster
        Swal.fire({
            title: 'Süre Doldu',
            text: 'Eğitim süresi doldu. Eğitim tamamlanmamış olarak işaretlendi.',
            icon: 'error',
            confirmButtonText: 'Tamam'
        }).then(() => {
            // AJAX ile iptal sebebini veritabanına kaydet
            fetch('mark_as_failed.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `user_id=<?php echo $userId; ?>&haber_id=<?php echo $haber_id; ?>`
            }).then(response => response.text()).then(data => {
                // Ana sayfaya yönlendir
                window.location.href = 'anasayfa';
            });
        });
    }

    function startTimer() {
        // Süre başlatılmamışsa başlat
        if (!localStorage.getItem(startTimeKey)) {
            localStorage.setItem(startTimeKey, Date.now());
        }
        setupTimer(); // Zamanlayıcıyı başlat
    }

    function setupTimer() {
        setInterval(updateTimer, 1000);
    }

    window.onload = function() {
        startTimer();
        updateTimer(); // Sayfa yüklendiğinde zamanlayıcıyı güncelle

        // Önceden kaydedilen video süresini ayarla
        const savedTime = localStorage.getItem(videoTimeKey);
        if (savedTime) {
            video.currentTime = parseFloat(savedTime);
        }

        // Test yapılmışsa ve video tamamlanmışsa butonları ayarla
        const testDone = localStorage.getItem('test_done') === videoId;
        const videoCompleted = localStorage.getItem(videoCompletedKey) === 'true';

        if (testDone) {
            testButton.style.display = 'none';
            videoContainer.style.display = 'block';
            playButton.style.display = 'block';

            if (videoCompleted) {
                sinaviGecBtn.disabled = false;
                sinaviGecBtn.style.display = 'block';
                reloadMessage.style.display = 'block';
                playButton.style.display = 'none'; // Video tamamlandıktan sonra Oynat butonunu gizle
            } else {
                sinaviGecBtn.disabled = true;
                sinaviGecBtn.style.display = 'none';
                reloadMessage.style.display = 'none';
            }
        } else {
            videoContainer.style.display = 'none';
            playButton.style.display = 'none';
        }
    };

    testButton.addEventListener('click', function() {
        localStorage.setItem('test_done', videoId);
        testMessage.style.display = 'block';
        testButton.style.display = 'none'; // Test butonunu gizle
        videoContainer.style.display = 'block';
        playButton.style.display = 'block';
    });

    playButton.addEventListener('click', function() {
        videoContainer.style.display = 'block';
        video.style.display = 'block';
        video.play();
        playButton.style.display = 'none'; // Oynat butonunu gizle

        if (video.requestFullscreen) {
            video.requestFullscreen();
        } else if (video.webkitRequestFullscreen) {
            video.webkitRequestFullscreen();
        } else if (video.msRequestFullscreen) {
            video.msRequestFullscreen();
        }

        video.controls = false;
    });

    video.addEventListener('ended', function() {
        sinaviGecBtn.disabled = false;
        sinaviGecBtn.style.display = 'block';
        reloadMessage.style.display = 'block';

        video.style.display = 'none';
        videoContainer.style.display = 'none';

        // Oynat butonunu tamamen gizle
        playButton.style.display = 'none'; 

        if (document.fullscreenElement) {
            document.exitFullscreen();
        }

        localStorage.setItem(videoCompletedKey, 'true');
        localStorage.removeItem(videoTimeKey);
    });

    video.addEventListener('pause', function() {
        localStorage.setItem(videoTimeKey, video.currentTime);
    });

    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            video.pause();
            video.style.display = 'none';
            videoContainer.style.display = 'none';
            playButton.style.display = 'block'; // Oynat butonunu göster
            localStorage.setItem(videoTimeKey, video.currentTime);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            }
        }
    });

    sinaviGecBtn.addEventListener('click', function() {
        // Veritabanında tamamlandığını işaretle
        fetch('mark_as_completed.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `user_id=<?php echo $userId; ?>&haber_id=<?php echo $haber_id; ?>`
        }).then(response => response.text()).then(data => {
            message.style.display = 'block'; // Tamamlandı mesajını göster
            window.location.href = sinavUrl;
        });
    });

    window.onbeforeunload = function() {
        return 'Sayfayı yenilerseniz başa dönersiniz!';
    };
</script>




<br><br><br><br><br><br>
<?php include("inc/footer.php"); ?>
