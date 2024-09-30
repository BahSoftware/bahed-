<?php
include_once("inc/header.php");
include_once("access_control.php");
// Mesaj değişkenini başlat
$message = '';
$showMessage = false;

try {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Silme işlemi
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $examId = intval($_GET['id']);
        $sql = "DELETE FROM exam_results WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $examId]);

        $message = "Sonuç başarıyla silindi!";
        $showMessage = true;
    }

    // Sınav sonuçlarını listeleme
    $sorgu = $db->query("SELECT * FROM exam_results")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Hata: " . htmlspecialchars($e->getMessage());
    $showMessage = true;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Sonuçları</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Genel buton stili */
.btn-custom {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 8px; /* Daha yuvarlak köşeler */
    padding: 12px 20px; /* Daha geniş padding */
    font-size: 16px;
    font-weight: 600; /* Daha belirgin metin */
    color: #fff; /* Metin rengi */
    cursor: pointer;
    transition: all 0.3s ease; /* Yumuşak geçiş */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-transform: uppercase; /* Metin büyük harfli */
}

/* Düzenle butonu */
.btn-edit {
    background-color: #007bff; /* Açık mavi */
}

.btn-edit:hover {
    background-color: #0056b3; /* Daha koyu mavi */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Daha belirgin gölge */
    transform: translateY(-2px); /* Hover sırasında butonu kaldır */
}

/* Sil butonu */
.btn-delete {
    background-color: #dc3545; /* Kırmızı */
}

.btn-delete:hover {
    background-color: #c82333; /* Daha koyu kırmızı */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Daha belirgin gölge */
    transform: translateY(-2px); /* Hover sırasında butonu kaldır */
}

/* İkonlar için stil */
.btn-custom i {
    color: #fff; /* İkonların rengi */
    margin-right: 8px; /* İkon ile metin arasında boşluk */
}
/* Sil butonu için ikon rengi */
.btn-delete i {
    color: red; /* İkonun rengi */
}
        .alert-box {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 90%;
            width: 400px;
            padding: 15px;
            border-radius: 5px;
            background-color: #d4edda; /* Success color */
            color: #155724;
            border: 1px solid #c3e6cb;
            display: none; /* Initially hidden */
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .alert-box.error {
            background-color: #f8d7da; /* Error color */
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-box .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        .alert-box .close:hover {
            color: #000;
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            color: #fff;
        }
        .btn-custom i {
            margin-right: 8px;
        }
        .card-header .btn {
            margin-left: auto;
        }
    </style>
</head>
<body>
    <!-- Mesaj Kutusu -->
    <div class="alert-box <?php echo $showMessage ? '' : 'd-none'; ?> <?php echo isset($error) ? 'error' : ''; ?>" id="alertBox">
        <span class="close" onclick="closeAlert()">&times;</span>
        <?php echo htmlspecialchars($message); ?>
    </div>

    <!-- Kalan İçerik -->
    <div class="main-panel">
        <div class="content">
            <div class="page-inner">
                <div class="page-header">
                    <ul class="breadcrumbs">
                        <li class="nav-home">
                            <a href="#">
                                <i class="flaticon-home"></i>
                            </a>
                        </li>
                        <li class="separator">
                            <i class="flaticon-right-arrow"></i>
                        </li>
                        <li class="nav-item">
                            <a href="#">Sınavlar</a>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h4 class="card-title mb-0">Mevcut Sonuç Listesi</h4>
                                <a href="anasayfa" class="btn btn-primary btn-round ml-auto">
                                    <i class="fa fa-home"></i>
                                    Ana Sayfaya Dön
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="add-row" class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>İsim</th>
                                                <th>Puan</th>
                                                <th style="width: 20%">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>İsim</th>
                                                <th>Puan</th>
                                                <th>İşlem</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php foreach ($sorgu as $sonuc): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($sonuc['username']) ?></td>
                                                    <td><?= htmlspecialchars($sonuc['score']) ?></td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <!-- Sil Butonu -->
                                                            <button type="button" class="btn-custom btn-delete" data-toggle="tooltip" title="Sil" onclick="sil(<?= $sonuc['id'] ?>)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        function sil(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu sınavı silmek istediğinize emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet',
                cancelButtonText: 'İptal',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'sınav.php?action=delete&id=' + id;
                }
            });
        }

        // Mesaj kutusunu göster
        document.addEventListener('DOMContentLoaded', function() {
            var alertBox = document.getElementById('alertBox');
            if (alertBox) {
                alertBox.style.display = 'block';
                // Saklama işlemi
                sessionStorage.setItem('alertMessageShown', 'true');
            }
        });

        // Kapatma işlevi
        function closeAlert() {
            var alertBox = document.getElementById('alertBox');
            if (alertBox) {
                alertBox.style.display = 'none';
                // Saklama işlemi
                sessionStorage.setItem('alertMessageShown', 'true');
            }
        }

        // Sayfa yüklendiğinde mesajın gösterilmesini kontrol et
        document.addEventListener('DOMContentLoaded', function() {
            if (sessionStorage.getItem('alertMessageShown') === 'true') {
                var alertBox = document.getElementById('alertBox');
                if (alertBox) {
                    alertBox.style.display = 'none';
                }
            }
        });
    </script>
</body>
<?php include("inc/footer.php");?>
</html>

