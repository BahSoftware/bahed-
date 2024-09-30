<?php
include_once("inc/header.php");


function uploadFile($file, $allowedTypes, $targetDir) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowedTypes)) {
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $targetFile = $targetDir . uniqid() . '-' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                return $targetFile;
            } else {
                return "Dosya yükleme hatası.";
            }
        } else {
            return "Geçersiz dosya türü.";
        }
    }
    return "Dosya yükleme hatası.";
}

if (isset($_POST['kaydet'])) {
    $kategori = trim($_POST['kategori']);
    $baslik = trim($_POST['baslik']);
    $aciklama = trim(strip_tags($_POST['aciklama']));
    $sinav_url = trim($_POST['sinav_url']);
    $departman_id = isset($_POST['departman']) ? intval($_POST['departman']) : null;
    $on_test_url = isset($_POST['on_test_url']) ? trim($_POST['on_test_url']) : null;
    $on_test_check = isset($_POST['on_test_check']) ? true : false;

    // Sınav başlangıç ve bitiş tarihlerini al
    $sinav_baslangic = trim($_POST['sinav_baslangic']);
    $sinav_bitis = trim($_POST['sinav_bitis']);

    // Tarih formatını doğrulama
    if ($sinav_baslangic && !DateTime::createFromFormat('Y-m-d\TH:i', $sinav_baslangic)) {
        echo "Geçersiz sınav başlangıç tarihi formatı.";
        exit;
    }
    
    if ($sinav_bitis && !DateTime::createFromFormat('Y-m-d\TH:i', $sinav_bitis)) {
        echo "Geçersiz sınav bitiş tarihi formatı.";
        exit;
    }
    
    // Kapak resmi yükleme
    $kapak_resmi = $_FILES['kapak_resmi'];
    $kapak_resmi_hedef_dosya = uploadFile($kapak_resmi, ['image/jpeg', 'image/png', 'image/gif'], "./eğitim/kapak_resmi/");
    if (strpos($kapak_resmi_hedef_dosya, 'Dosya yükleme hatası') !== false || strpos($kapak_resmi_hedef_dosya, 'Geçersiz dosya türü') !== false) {
        echo $kapak_resmi_hedef_dosya;
        exit;
    }

    // Video dosyası yükleme
    $video = $_FILES['video'];
    $video_hedef_dosya = uploadFile($video, ['video/mp4', 'video/avi', 'video/mkv'], "./eğitim/video/");

    // PDF dosyası yükleme
    $pdf = $_FILES['pdf'];
    $pdf_hedef_dosya = uploadFile($pdf, ['application/pdf'], "./eğitim/pdf/");

    // SQL sorgusunun hazırlanması
    $sql = "INSERT INTO bahedu (kategori, baslik, foto, video, pdf, sinav_url, aciklama, on_test_url, on_test, sinav_baslangic, sinav_bitis) 
            VALUES (:kategori, :baslik, :foto, :video, :pdf, :sinav_url, :aciklama, :on_test_url, :on_test, :sinav_baslangic, :sinav_bitis)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':kategori', $kategori);
    $stmt->bindParam(':baslik', $baslik);
    $stmt->bindParam(':foto', $kapak_resmi_hedef_dosya);
    $stmt->bindParam(':video', $video_hedef_dosya);
    $stmt->bindParam(':pdf', $pdf_hedef_dosya);
    $stmt->bindParam(':sinav_url', $sinav_url);
    $stmt->bindParam(':aciklama', $aciklama);
    $stmt->bindParam(':on_test_url', $on_test_url);
    $stmt->bindParam(':on_test', $on_test_check, PDO::PARAM_BOOL);
    $stmt->bindParam(':sinav_baslangic', $sinav_baslangic);
    $stmt->bindParam(':sinav_bitis', $sinav_bitis);

    try {
        if ($stmt->execute()) {
            $lastInsertedId = $db->lastInsertId();
            
            // İçerik erişimi ayarlama
            $accessSql = "INSERT INTO content_access (content_id, user_id) VALUES (:content_id, :user_id)";
            $accessStmt = $db->prepare($accessSql);

            if ($departman_id !== null) {
                // Seçilen departmandaki kullanıcılara erişim ver
                $userQuery = $db->prepare("SELECT id FROM kulanicilar WHERE departman = :departman");
                $userQuery->bindParam(':departman', $departman_id);
                $userQuery->execute();
                $users = $userQuery->fetchAll(PDO::FETCH_COLUMN);

                foreach ($users as $user_id) {
                    $accessStmt->bindParam(':content_id', $lastInsertedId);
                    $accessStmt->bindParam(':user_id', $user_id);
                    $accessStmt->execute();
                }
            } else {
                // Kullanıcı seçilmemişse tüm kullanıcılara erişim ver
                $userQuery = $db->query("SELECT id FROM kulanicilar");
                $users = $userQuery->fetchAll(PDO::FETCH_COLUMN);

                foreach ($users as $user_id) {
                    $accessStmt->bindParam(':content_id', $lastInsertedId);
                    $accessStmt->bindParam(':user_id', $user_id);
                    $accessStmt->execute();
                }
            }

            echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
            echo "<script>
                swal({
                    title: 'Başarılı!',
                    text: 'Eğitim başarıyla eklendi!',
                    icon: 'success',
                    button: 'Tamam'
                }).then(function() {
                    window.location.href = 'eğitim.php';
                });
            </script>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Veritabanına ekleme hatası: " . htmlspecialchars($errorInfo[2]);
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
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Kategori Seçimi -->
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
                                                            echo "<option value='".htmlspecialchars($cikti["id"])."'>".htmlspecialchars($cikti["kategori_name"])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Kullanıcı Seçimi -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="kullanici">Kullanıcı Seç (isteğe bağlı)</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select id="kullanici" name="kullanici[]" class="js-example-basic-single w-100" multiple>
                                                    <option disabled>Seçiniz</option>
                                                    <?php 
                                                        $sorgu = $db->query("SELECT * FROM kulanicilar");
                                                        while ($cikti = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='".htmlspecialchars($cikti["id"])."'>".htmlspecialchars($cikti["kullanici_ad"])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Departman Seçimi -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="departman">Departman Seç (isteğe bağlı)</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select id="departman" name="departman[]" class="js-example-basic-single w-100" multiple>
                                                    <option disabled>Seçiniz</option>
                                                    <?php 
                                                        $sorgu = $db->query("SELECT * FROM departman");
                                                        while ($cikti = $sorgu->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='".htmlspecialchars($cikti["id"])."'>".htmlspecialchars($cikti["Departman_adi"])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Başlık -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="baslik">Başlık *</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" id="baslik" required class="form-control" placeholder="Başlık" name="baslik">
                                            </div>
                                        </div>
                                        
                                        <!-- Kapak Resmi -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="kapak_resmi">Kapak Resmi Yükle *</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="file" id="kapak_resmi" class="form-control" name="kapak_resmi" accept="image/*" required>
                                            </div>
                                        </div>

                                        <!-- Video Yükleme -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="video">Video Yükle</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="file" id="video" class="form-control" name="video" accept="video/*">
                                            </div>
                                        </div>

                                        <!-- PDF Yükleme -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="pdf">PDF Yükle</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="file" id="pdf" class="form-control" name="pdf" accept=".pdf">
                                            </div>
                                        </div>

                                        <!-- Sınav URL'si -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="sinav_url">Sınav URL'si *</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="url" id="sinav_url" required class="form-control" placeholder="Sınav URL'sini girin" name="sinav_url">
                                            </div>
                                        </div>

                                        <!-- Açıklama -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="aciklama">Açıklama</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" id="aciklama" name="aciklama" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <!-- Ön Test -->
                                        <div class="form-group row">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="on_test_check">Ön Test Yapılacak mı?</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="checkbox" id="on_test_check" name="on_test_check">
                                            </div>
                                        </div>

                                        <!-- Ön Test URL -->
                                        <div class="form-group row d-none" id="on_test_url_group">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="on_test_url">Ön Test URL'si</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="url" id="on_test_url" class="form-control" name="on_test_url" placeholder="Ön test URL'sini girin">
                                            </div>
                                        </div>

                                        <!-- Sınav Başlangıç Tarihi -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="sinav_baslangic">Sınav Başlangıç Tarihi *</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="datetime-local" id="sinav_baslangic" required class="form-control" name="sinav_baslangic">
                                            </div>
                                        </div>

                                        <!-- Sınav Bitiş Tarihi -->
                                        <div class="form-group row validate">
                                            <div class="col-sm-3">
                                                <label class="control-label" for="sinav_bitis">Sınav Bitiş Tarihi *</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="datetime-local" id="sinav_bitis" required class="form-control" name="sinav_bitis">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-action">
                                    <input type="hidden" class="form-control" name="birimekle">
                                    <button name="kaydet" type="submit" class="btn btn-success">Ekle</button>
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
    document.getElementById('on_test_check').addEventListener('change', function() {
        var urlGroup = document.getElementById('on_test_url_group');
        if (this.checked) {
            urlGroup.classList.remove('d-none');
        } else {
            urlGroup.classList.add('d-none');
        }
    });
</script>


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
