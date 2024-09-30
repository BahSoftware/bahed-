<!-- Include SweetAlert2 CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<?php
include("inc/header.php");

// Kullanıcının oturum açmış olduğunu ve $userId değişkeninin mevcut olduğunu varsayıyoruz.
if ($userId > 0) {
    // Kullanıcının departman ID'sini al
    $departmanQuery = $db->prepare("SELECT departman FROM kulanicilar WHERE id = :user_id");
    $departmanQuery->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $departmanQuery->execute();
    $departmanId = $departmanQuery->fetchColumn();

    // Eğer departman ID'si bulunamazsa
    if ($departmanId === false) {
        echo "Kullanıcı departman bilgisi bulunamadı.";
        exit;
    }

    // İçerikleri departman ID'sine göre veya genel erişim bazında filtrele
    $sql = $db->prepare("
        SELECT DISTINCT b.*
        FROM bahedu b
        LEFT JOIN content_access ca ON b.id = ca.content_id
        WHERE (ca.user_id = :user_id OR ca.user_id IS NULL)
          AND (b.sinav_bitis IS NULL OR b.sinav_bitis >= NOW())
          AND (b.id IN (
              SELECT DISTINCT b.id
              FROM bahedu b
              LEFT JOIN content_access ca ON b.id = ca.content_id
              LEFT JOIN kulanicilar k ON ca.user_id = k.id
              WHERE k.departman = :departman
              OR ca.user_id IS NULL
          ))
    ");
    $sql->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $sql->bindParam(':departman', $departmanId, PDO::PARAM_INT);
    $sql->execute();
    $haberler = $sql->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Kullanıcı bilgisi bulunamadı.";
    exit;
}
?>

<style>
    .hero-section {
        background-image: linear-gradient(to right, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0) 100%), url('img/carousel-2.jpg');
        background-size: cover;
        background-position: center;
        height: 300px; /* İhtiyaca göre ayarlayın */
        display: flex;
        align-items: center;
        justify-content: center; /* Yatayda ortalamak için */
        color: white;
        text-align: center; /* Yazıyı ortalamak için */
        padding: 0 20px;
        border-bottom: 5px solid #007bff; /* Alt sınır rengi */
        position: relative; /* İçerikleri doğru yerleştirmek için */
        overflow: hidden; /* Arka planın taşmasını engellemek için */
    }

    .hero-section h1 {
        font-size: 2.5rem; /* Başlık boyutunu ihtiyaca göre ayarlayın */
        font-weight: bold;
        margin: 0;
        text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7); /* Başlık gölgesi */
        color: white;
        max-width: 90%; /* Başlık genişliğini kısıtlamak için */
        line-height: 1.2; /* Satır yüksekliği */
    }
</style>

<div class="hero-section">
    <h1>Kurslarım</h1>
</div>
<div class="container mt-5">
    <!-- Kurs ekleme ve sonuçları göster butonları -->
<div class="d-flex justify-content-end mb-3">
    <?php if ($userRole === '1'): ?>
        <a href="egitim" class="btn btn-primary mr-2">Kurs Ekle</a>
        <a href="sonuc" class="btn btn-primary">Sonuçları Göster</a>
    <?php endif; ?>
</div>

    
    <div class="row">
        <?php foreach ($haberler as $haber): 
            $haber_seo = $haber['baslik'];
            $haber_sef_link = sef_link($haber_seo);
            $detay_link = "kurs_detay.php?id=" . urlencode($haber['id']) . "/" . urlencode($haber_sef_link);
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <img src="<?php echo htmlspecialchars($haber['foto']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($haber['baslik']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($haber['baslik']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($haber['aciklama']); ?>.</p>
                    <a href="#" class="btn btn-primary start-course" data-course-id="<?php echo htmlspecialchars($haber['id']); ?>">Eğitime Başlama</a>
                </div>
                <div class="card-footer">
                    <small>
                        <?php if (!empty($haber['sinav_baslangic'])): ?>
                            Sınav Başlangıç: <?php echo htmlspecialchars($haber['sinav_baslangic']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($haber['sinav_bitis'])): ?>
                            Sınav Bitiş: <?php echo htmlspecialchars($haber['sinav_bitis']); ?><br>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.start-course').forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Varsayılan bağlantı davranışını durdur

                const courseId = button.getAttribute('data-course-id');
                
                Swal.fire({
                    title: 'Eğitime Başlamak Üzeresiniz',
                    text: "Eğitimi başlatmak istediğinizden emin misiniz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, Başlat',
                    cancelButtonText: 'Hayır, İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX ile veriyi gönder
                        fetch('start_course.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                'user_id': <?php echo $userId; ?>,
                                'course_id': courseId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Başarıyla kaydedildi, kullanıcıyı yönlendir
                                window.location.href = 'kurs_detay.php?id=' + courseId;
                            } else {
                                // Hata mesajını göster
                                Swal.fire({
                                    title: 'Hata',
                                    text: data.message || 'Bir hata oluştu.',
                                    icon: 'error',
                                    confirmButtonText: 'Tamam'
                                });
                            }
                        });
                    }
                });
            });
        });
    });
</script>

<?php include("inc/footer.php"); ?>
