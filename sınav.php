<?php
include_once("inc/header.php");
include_once("access_control.php");
// Veritabanı bağlantısı ve işlem
try {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Silme işlemi
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $examId = intval($_GET['id']);
        $sql = "DELETE FROM exams WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $examId]);

        $message = "Sınav başarıyla silindi!";
        $alertType = "success";
    }

    // Sınavları listeleme
    $sorgu = $db->query("SELECT * FROM exams")->fetchAll();
} catch (PDOException $e) {
    $message = "Hata: " . htmlspecialchars($e->getMessage());
    $alertType = "danger";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınavlar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
        /* Button Styles */
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-edit {
            background-color: #007bff;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }

        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        /* Düzenle butonu için ikon rengi */
.btn-edit i {
    color: blue; /* İkonun rengi */
}

/* Sil butonu için ikon rengi */
.btn-delete i {
    color: red; /* İkonun rengi */
}

    </style>
</head>
<body>
    <!-- Mesaj Kutusu -->
    <?php if (isset($message)): ?>
        <div class="alert-box <?php echo $alertType; ?>" id="alertBox">
            <span class="close" onclick="closeAlert()">&times;</span>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

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
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title mb-0">Mevcut hizmetler listesi</h4>
                                    <div class="btn-group ml-auto">
                                        <a href="egitimekle" class="btn btn-primary btn-round">
                                            <i class="fa fa-plus"></i>
                                            Eğitim Ekle
                                        </a>
                                        <a href="sinavekle" class="btn btn-primary btn-round ml-2">
                                            <i class="fa fa-plus"></i>
                                            Sınav Ekle
                                        </a>
                                        <a href="sonuc" class="btn btn-primary btn-round ml-2">
                                            <i class="fa fa-file-alt"></i>
                                            Sonuclar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="add-row" class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Başlık</th>
                                                <th>Açıklama</th>
                                                <th style="width: 20%">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Başlık</th>
                                                <th>Açıklama</th>
                                                <th>İşlem</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php foreach ($sorgu as $sonuc) { ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($sonuc['title']) ?></td>
                                                    <td><?= htmlspecialchars($sonuc['description']) ?></td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <button type="button" data-toggle="tooltip" title="Düzenle" class="btn btn-custom btn-edit" onclick="window.location.href='sınavdetay.php?id=<?= $sonuc['id'] ?>'">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <button type="button" data-toggle="tooltip" title="Sil" class="btn btn-custom btn-delete" onclick="sil(<?= $sonuc['id'] ?>)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
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
                setTimeout(function() {
                    alertBox.style.display = 'none';
                }, 5000); // 5 saniye sonra mesaj kutusunu gizle
            }
        });

        // Kapatma işlevi
        function closeAlert() {
            var alertBox = document.getElementById('alertBox');
            if (alertBox) {
                alertBox.style.display = 'none';
            }
        }
    </script>
</body>
</html>
