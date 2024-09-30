<?php
include_once("inc/header.php");

// Eğitim kaydının ID'sini al
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Eğer ID geçerli ise veritabanından veriyi al
if ($id > 0) {
    $stmt = $db->prepare("SELECT * FROM bahedu WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $egitim = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$egitim) {
        echo "Kayıt bulunamadı.";
        exit;
    }
}

// Kullanıcı erişim bilgilerini al
$userIds = [];
$accessStmt = $db->prepare("SELECT user_id FROM content_access WHERE content_id = :content_id");
$accessStmt->bindParam(':content_id', $id);
$accessStmt->execute();
while ($row = $accessStmt->fetch(PDO::FETCH_ASSOC)) {
    $userIds[] = $row['user_id'];
}

// Form submit edildiğinde
if (isset($_POST['guncelle'])) {
    // Formdan gelen verileri al
    $kategori = trim($_POST['kategori']);
    $baslik = trim($_POST['baslik']);
    $aciklama = trim(strip_tags($_POST['aciklama']));
    $sinav_url = trim($_POST['sinav_url']);
    $sinav_baslangic = trim($_POST['sinav_baslangic']);
    $sinav_bitis = trim($_POST['sinav_bitis']);
    $kullanici_ids = isset($_POST['kullanici']) ? $_POST['kullanici'] : [];
    $departman_id = isset($_POST['departman']) ? intval($_POST['departman']) : null;

    // Kapak resmi dosyasını kontrol et
    $kapak_resmi = $_FILES['kapak_resmi'];
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $kapak_resmi_hedef_dosya = $egitim['foto']; // Mevcut fotoğrafı varsayılan olarak al

    if ($kapak_resmi['error'] === UPLOAD_ERR_OK) {
        if (in_array($kapak_resmi['type'], $allowedImageTypes)) {
            $kapak_resmi_hedef_klasor = "../eğitim/kapak_resmi/";
            $kapak_resmi_hedef_dosya = $kapak_resmi_hedef_klasor . basename($kapak_resmi['name']);
            if (!move_uploaded_file($kapak_resmi['tmp_name'], $kapak_resmi_hedef_dosya)) {
                echo "Kapak resmi yükleme hatası.";
                exit;
            }
        } else {
            echo "Geçersiz kapak resmi dosyası türü.";
            exit;
        }
    }

    // Video dosyasını kontrol et
    $video = $_FILES['video'];
    $video_hedef_dosya = $egitim['video']; // Mevcut video

    if ($video['error'] === UPLOAD_ERR_OK) {
        $allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mkv'];
        if (in_array($video['type'], $allowedVideoTypes)) {
            $video_hedef_klasor = "../eğitim/video/";
            $video_hedef_dosya = $video_hedef_klasor . basename($video['name']);
            if (!move_uploaded_file($video['tmp_name'], $video_hedef_dosya)) {
                echo "Video yükleme hatası.";
                exit;
            }
        } else {
            echo "Geçersiz video dosyası türü.";
            exit;
        }
    }

    // PDF dosyasını kontrol et
    $pdf = $_FILES['pdf'];
    $pdf_hedef_dosya = $egitim['pdf']; // Mevcut PDF

    if ($pdf['error'] === UPLOAD_ERR_OK) {
        $allowedPdfTypes = ['application/pdf'];
        if (in_array($pdf['type'], $allowedPdfTypes)) {
            $pdf_hedef_dosya = "../eğitim/pdf/" . basename($pdf['name']);
            if (!move_uploaded_file($pdf['tmp_name'], $pdf_hedef_dosya)) {
                echo "PDF yükleme hatası.";
                exit;
            }
        } else {
            echo "Geçersiz PDF dosyası türü.";
            exit;
        }
    }

    // Veritabanına güncelleme sorgusu
    $sql = "UPDATE bahedu SET 
                kategori = :kategori, 
                baslik = :baslik, 
                foto = :foto, 
                video = :video, 
                pdf = :pdf, 
                sinav_url = :sinav_url,
                sinav_baslangic = :sinav_baslangic,
                sinav_bitis = :sinav_bitis,
                aciklama = :aciklama 
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':kategori', $kategori);
    $stmt->bindParam(':baslik', $baslik);
    $stmt->bindParam(':foto', $kapak_resmi_hedef_dosya);
    $stmt->bindParam(':video', $video_hedef_dosya);
    $stmt->bindParam(':pdf', $pdf_hedef_dosya);
    $stmt->bindParam(':sinav_url', $sinav_url);
    $stmt->bindParam(':sinav_baslangic', $sinav_baslangic);
    $stmt->bindParam(':sinav_bitis', $sinav_bitis);
    $stmt->bindParam(':aciklama', $aciklama);
    $stmt->bindParam(':id', $id);

    try {
        if ($stmt->execute()) {
            // Kullanıcı erişim bilgilerini güncelle
            $accessSql = "DELETE FROM content_access WHERE content_id = :content_id";
            $accessStmt = $db->prepare($accessSql);
            $accessStmt->bindParam(':content_id', $id);
            $accessStmt->execute();

            // Kullanıcı seçilmediyse tüm kullanıcılara erişim ver
            if (!empty($kullanici_ids)) {
                // Kullanıcı IDs listesine göre erişim ver
                $accessSql = "INSERT INTO content_access (content_id, user_id) VALUES (:content_id, :user_id)";
                $accessStmt = $db->prepare($accessSql);

                foreach ($kullanici_ids as $user_id) {
                    $accessStmt->bindParam(':content_id', $id);
                    $accessStmt->bindParam(':user_id', $user_id);
                    $accessStmt->execute();
                }
            } else {
                if ($departman_id !== null) {
                    // Belirli bir departmandaki kullanıcılara erişim ver
                    $userQuery = $db->prepare("SELECT id FROM kulanicilar WHERE departman = :departman_id");
                    $userQuery->bindParam(':departman_id', $departman_id);
                    $userQuery->execute();
                    $users = $userQuery->fetchAll(PDO::FETCH_COLUMN);

                    $accessSql = "INSERT INTO content_access (content_id, user_id) VALUES (:content_id, :user_id)";
                    $accessStmt = $db->prepare($accessSql);

                    foreach ($users as $user_id) {
                        $accessStmt->bindParam(':content_id', $id);
                        $accessStmt->bindParam(':user_id', $user_id);
                        $accessStmt->execute();
                    }
                } else {
                    // Kullanıcı seçilmediyse tüm kullanıcılara erişim ver
                    $userQuery = $db->query("SELECT id FROM kulanicilar");
                    $users = $userQuery->fetchAll(PDO::FETCH_COLUMN);

                    $accessSql = "INSERT INTO content_access (content_id, user_id) VALUES (:content_id, :user_id)";
                    $accessStmt = $db->prepare($accessSql);

                    foreach ($users as $user_id) {
                        $accessStmt->bindParam(':content_id', $id);
                        $accessStmt->bindParam(':user_id', $user_id);
                        $accessStmt->execute();
                    }
                }
            }

            // Başarılı bildirim
            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            echo "<script>
                swal({
                    title: 'Başarılı!',
                    text: 'Eğitim başarıyla güncellendi!',
                    icon: 'success',
                    button: 'Tamam'
                }).then(function() {
                    window.location.href = 'eğitim.php'; // Yönlendirme yapabilirsiniz
                });
            </script>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Veritabanına güncelleme hatası: " . htmlspecialchars($errorInfo[2]);
        }
    } catch (PDOException $e) {
        echo "Veritabanı hatası: " . htmlspecialchars($e->getMessage());
    }
}
?>

<br><br><br>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="kategori">Kategori *</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select id="kategori" name="kategori" class="js-example-basic-single w-100" required>
                                            <option selected disabled>Seçiniz</option>
                                            <?php 
                                                $sorgu = $db->query("SELECT * FROM kategori");
                                                while ($cikti = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = ($cikti['id'] == $egitim['kategori']) ? 'selected' : '';
                                                    echo "<option value='".$cikti["id"]."' $selected>".$cikti["kategori_name"]."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="kullanici">Kullanıcı Seç (isteğe bağlı)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select id="kullanici" name="kullanici[]" class="js-example-basic-single w-100" multiple>
                                            <?php 
                                                $sorgu = $db->query("SELECT * FROM kulanicilar");
                                                while ($cikti = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = in_array($cikti["id"], $userIds) ? 'selected' : '';
                                                    echo "<option value='".$cikti["id"]."' $selected>".$cikti["kullanici_ad"]."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="departman">Departman Seç (isteğe bağlı)</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select id="departman" name="departman" class="js-example-basic-single w-100">
                                            <option value="">Seçiniz</option>
                                            <?php 
                                                $sorgu = $db->query("SELECT * FROM departman");
                                                while ($cikti = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = ($cikti["id"] == $departman_id) ? 'selected' : '';
                                                    echo "<option value='".$cikti["id"]."' $selected>".$cikti["Departman_adi"]."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="baslik">Başlık *</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="baslik" required class="form-control" placeholder="Başlık" name="baslik" value="<?php echo htmlspecialchars($egitim['baslik']); ?>">
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="kapak_resmi">Kapak Resmi Yükle *</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file" id="kapak_resmi" class="form-control" name="kapak_resmi" accept="image/*">
                                        <small>Mevcut: <a href="<?php echo htmlspecialchars($egitim['foto']); ?>" target="_blank">Görüntüle</a></small>
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="video">Video Yükle</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file" id="video" class="form-control" name="video" accept="video/*">
                                        <small>Mevcut: <a href="<?php echo htmlspecialchars($egitim['video']); ?>" target="_blank">Görüntüle</a></small>
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="pdf">PDF Yükle</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file" id="pdf" class="form-control" name="pdf" accept=".pdf">
                                        <small>Mevcut: <a href="<?php echo htmlspecialchars($egitim['pdf']); ?>" target="_blank">Görüntüle</a></small>
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="sinav_url">Sınav URL'si *</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="url" id="sinav_url" required class="form-control" placeholder="Sınav URL'sini girin" name="sinav_url" value="<?php echo htmlspecialchars($egitim['sinav_url']); ?>">
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="sinav_baslangic">Sınav Başlangıç Tarihi ve Saati *</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" id="sinav_baslangic" required class="form-control" name="sinav_baslangic" value="<?php echo htmlspecialchars($egitim['sinav_baslangic']); ?>">
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="sinav_bitis">Sınav Bitiş Tarihi ve Saati *</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" id="sinav_bitis" required class="form-control" name="sinav_bitis" value="<?php echo htmlspecialchars($egitim['sinav_bitis']); ?>">
                                    </div>
                                </div>

                                <div class="form-group row validate">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="aciklama">Açıklama</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="aciklama" name="aciklama" rows="3"><?php echo htmlspecialchars($egitim['aciklama']); ?></textarea>
                                    </div>
                                </div>

                                <div class="card-action">
                                    <button name="guncelle" type="submit" class="btn btn-success">Güncelle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    tinymce.init({
        selector: '#aciklama',
        plugins: 'link image code',
        toolbar: 'undo redo | link image | code',
    });
</script>

<?php
include_once("inc/footer.php");
?>
