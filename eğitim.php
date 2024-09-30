<?php
include_once("inc/header.php");
include_once("access_control.php");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hizmetler Listesi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            color: blue;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }
/* Düzenle butonu için ikon rengi */
.btn-edit i {
    color: blue; /* İkonun rengi */
}

/* Sil butonu için ikon rengi */
.btn-delete i {
    color: red; /* İkonun rengi */
}

        .btn-primary-custom {
            background-color: #007bff;
        }
        .btn-primary-custom:hover {
            background-color: #0056b3;
        }

        .btn-danger-custom {
            background-color: #dc3545;
        }
        .btn-danger-custom:hover {
            background-color: #c82333;
        }

        .table img {
            max-width: 140px;
            max-height: 120px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <br><br><br>
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
                        <li class="separator">
                            <i class="flaticon-right-arrow"></i>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title mb-0">Mevcut Hizmetler Listesi</h4>
                                    <div class="btn-group ml-auto">
                                        <a href="egitimekle" class="btn btn-primary btn-round btn-custom btn-primary-custom btn-edit">
                                            <i class="fa fa-plus"></i>
                                            Eğitim Ekle
                                        </a>
                                        <a href="sinav" class="btn btn-primary btn-round ml-2 btn-custom btn-primary-custom btn-edit">
                                            <i class="fa fa-plus"></i>
                                            Sınav Ekle
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="add-row" class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Kategori</th>
                                                <th>Başlık</th>
                                                <th>Kapak Resmi</th>
                                                <th style="width: 10%">İşlem</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        // Veritabanı sorgusu
                                        $sorgu = $db->query("SELECT * FROM bahedu")->fetchAll();
                                        foreach ($sorgu as $sonuc) {
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($sonuc['kategori']); ?></td>
                                                <td><?= htmlspecialchars($sonuc['baslik']); ?></td>
                                                <td>
                                                    <?php if (!empty($sonuc['foto'])): ?>
                                                        <img src="<?= htmlspecialchars($sonuc['foto']); ?>" alt="image"/>
                                                    <?php else: ?>
                                                        <span>Resim yok</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="form-button-action">
                                                        <button type="button" data-toggle="tooltip" title="Düzenle" class="btn btn-link btn-primary btn-lg btn-custom btn-primary-custom btn-edit" onclick="window.location.href='egitim_detay.php?id=<?= $sonuc["id"] ?>'">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" data-toggle="tooltip" title="Sil" class="btn btn-link btn-danger btn-lg btn-custom btn-danger-custom btn-delete" onclick="confirmDelete(<?= $sonuc['id'] ?>)">
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
    
    <script>
        function confirmDelete(id) {
            if (confirm("Bu kaydı silmek istediğinizden emin misiniz?")) {
                window.location.href = 'egitim_sil.php?egitim_id=' + id;
            }
        }
    </script>
</body>
</html>
