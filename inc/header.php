<?php
include("inc/data.php");
include("function.php");


// Kullanıcının oturum açıp açmadığını kontrol et

session_start(); // Oturum başlatmayı unutmayın
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if (!isset($_SESSION['user_id'])) {
    // Kullanıcı giriş yapmamışsa, login.php'ye yönlendir
    header('Location: login.php');
    exit(); // Yönlendirme sonrası kodun çalışmasını durdur
}

// Kullanıcı adını ve rolünü almak için veritabanı sorgusu
$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT kullanici_ad, user_rol FROM kulanicilar WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $userName = $user['kullanici_ad'];
    $userRole = $user['user_rol'];
} else {
    $userName = 'Kullanıcı';
    $userRole = 'Unknown'; // Rol bilgisi mevcut değilse varsayılan değer
}

// Kullanıcı rolüne göre kısıtlama koyma işlemi
if ($userRole === '1') {
    // Admin kullanıcı için özel işlemler
} elseif ($userRole === '2') {
    // Editor kullanıcı için özel işlemler
} else {
    // Diğer kullanıcı türleri için işlemler
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
   <!-- basic -->
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <!-- mobile metas -->
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="viewport" content="initial-scale=1, maximum-scale=1">
   <!-- site metas -->
   <title>BAHEDÜ</title>
   <meta name="keywords" content="">
   <meta name="description" content="">
   <meta name="author" content="">
   <!-- bootstrap css -->
   <link rel="stylesheet" href="css/bootstrap.min.css">
   <!-- style css -->
   <link rel="stylesheet" href="css/style.css">
   <!-- Responsive-->
   <link rel="stylesheet" href="css/responsive.css">
   <!-- Font Awesome CSS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <!-- fevicon -->
   <link rel="icon" href="images/icon.png" type="image/png" />
   <!-- Scrollbar Custom CSS -->
   <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
   <!-- Tweaks for older IEs-->
   <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">



   <script src="https://cdn.tiny.cloud/1/9zgrzvuvdumhh52ze85u5me9o2m1hvypusyeeoi005h1mebf/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
   <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<style>
    @import url('https://fonts.googleapis.com/css?family=Roboto');

body{
	font-family: 'Roboto', sans-serif;
}
* {
	margin: 0;
	padding: 0;
}
i {
	margin-right: 10px;
}
/*----------bootstrap-navbar-css------------*/
.navbar-logo{
	padding: 15px;
	color: #fff;
}
.navbar-mainbg{
	background-color: #5161ce;
	padding: 0px;
}
#navbarSupportedContent{
	overflow: hidden;
	position: relative;
}
#navbarSupportedContent ul{
	padding: 0px;
	margin: 0px;
}
#navbarSupportedContent ul li a i{
	margin-right: 10px;
}
#navbarSupportedContent li {
	list-style-type: none;
	float: left;
}
#navbarSupportedContent ul li a{
	color: rgba(255,255,255,0.5);
    text-decoration: none;
    font-size: 15px;
    display: block;
    padding: 20px 20px;
    transition-duration:0.6s;
	transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
    position: relative;
}
#navbarSupportedContent>ul>li.active>a{
	color: #5161ce;
	background-color: transparent;
	transition: all 0.7s;
}
#navbarSupportedContent a:not(:only-child):after {
	content: "\f105";
	position: absolute;
	right: 20px;
	top: 10px;
	font-size: 14px;
	font-family: "Font Awesome 5 Free";
	display: inline-block;
	padding-right: 3px;
	vertical-align: middle;
	font-weight: 900;
	transition: 0.5s;
}
#navbarSupportedContent .active>a:not(:only-child):after {
	transform: rotate(90deg);
}
.hori-selector{
	display:inline-block;
	position:absolute;
	height: 100%;
	top: 0px;
	left: 0px;
	transition-duration:0.6s;
	transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
	background-color: #fff;
	border-top-left-radius: 15px;
	border-top-right-radius: 15px;
	margin-top: 10px;
}
.hori-selector .right,
.hori-selector .left{
	position: absolute;
	width: 25px;
	height: 25px;
	background-color: #fff;
	bottom: 10px;
}
.hori-selector .right{
	right: -25px;
}
.hori-selector .left{
	left: -25px;
}
.hori-selector .right:before,
.hori-selector .left:before{
	content: '';
    position: absolute;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #5161ce;
}
.hori-selector .right:before{
	bottom: 0;
    right: -25px;
}
.hori-selector .left:before{
	bottom: 0;
    left: -25px;
}


@media(min-width: 992px){
	.navbar-expand-custom {
	    -ms-flex-flow: row nowrap;
	    flex-flow: row nowrap;
	    -ms-flex-pack: start;
	    justify-content: flex-start;
	}
	.navbar-expand-custom .navbar-nav {
	    -ms-flex-direction: row;
	    flex-direction: row;
	}
	.navbar-expand-custom .navbar-toggler {
	    display: none;
	}
	.navbar-expand-custom .navbar-collapse {
	    display: -ms-flexbox!important;
	    display: flex!important;
	    -ms-flex-preferred-size: auto;
	    flex-basis: auto;
	}
}


@media (max-width: 991px){
	#navbarSupportedContent ul li a{
		padding: 12px 30px;
	}
	.hori-selector{
		margin-top: 0px;
		margin-left: 10px;
		border-radius: 0;
		border-top-left-radius: 25px;
		border-bottom-left-radius: 25px;
	}
	.hori-selector .left,
	.hori-selector .right{
		right: 10px;
	}
	.hori-selector .left{
		top: -25px;
		left: auto;
	}
	.hori-selector .right{
		bottom: -25px;
	}
	.hori-selector .left:before{
		left: -25px;
		top: -25px;
	}
	.hori-selector .right:before{
		bottom: -25px;
		left: -25px;
	}
}
        /* Çıkış butonunun başlangıçta gizlenmesi */
        .logout-btn {
            display: none; /* Başlangıçta gizli */
            color: white;
            background-color: red;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .user-greeting {
            cursor: pointer; /* Kullanıcı adının üzerine tıklanabilir olduğunu göstermek için */
            color: white;
            font-size: 18px;
        }
    </style>
<body>
<!-- loader -->
<div class="loader_bg light-mode">
        <div class="loader"><img src="images/loading.gif" alt="#" /></div>
    </div>
    <!-- end loader -->

    <nav class="navbar navbar-expand-custom navbar-mainbg">
    <a class="navbar-brand navbar-logo" href="#">BahEğitim</a>
    <button class="navbar-toggler" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars text-white"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <div class="hori-selector"><div class="left"></div><div class="right"></div></div>
            <li class="nav-item">
                <a class="nav-link" href="anasayfa"><i class="fas fa-home"></i>Ana Sayfa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="hakkimizda"><i class="fas fa-info-circle"></i>Hakkımızda</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="kurslarım"><i class="fas fa-book"></i>Kurslarım</a>
            </li>
            <li class="nav-item">
            <?php if ($userRole === '1'): ?>
                <a class="nav-link" href="kullanicilar"><i class="fas fa-user"></i>Kullanıcılar</a>
                <?php endif; ?>
            </li>
            <!-- user kullancı -->
            <li class="nav-item user-name" onclick="toggleExitButton()">
                <a class="nav-link" href="#"><i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?></a>
            </li>
           <!-- Çıkış Butonu -->
            <li class="nav-item exit-btn">
                <a class="nav-link btn text-white" href="cikis">
                    <i class="fas fa-sign-out-alt"></i> Çıkış
                </a>
            </li>

        </ul>
    </div>
</nav>

<!-- CSS -->
<style>
    .exit-btn {
        display: none; /* Başlangıçta görünmez */
    }

    .user-name {
        cursor: pointer; /* Tıklanabilir göster */
    }

    .exit-btn.show {
        display: block; /* Çıkış butonunu gösterir */
    }
</style>
<script>function toggleExitButton() {
    var exitBtn = document.querySelector('.exit-btn');
    // Çıkış butonunun görünürlüğünü kontrol et
    if (exitBtn.classList.contains('show')) {
        exitBtn.classList.remove('show');
    } else {
        exitBtn.classList.add('show');
    }
}

// Ayrıca sayfa yüklendiğinde çıkış butonunu gizli tutmak için
document.addEventListener('DOMContentLoaded', function () {
    var exitBtn = document.querySelector('.exit-btn');
    exitBtn.classList.remove('show'); // Sayfa yüklendiğinde buton gizli olacak
});
</script>


   <!-- JavaScript files -->
   <script src="js/jquery.min.js"></script>
   <script src="js/bootstrap.bundle.min.js"></script>
   <script src="js/jquery-3.0.0.min.js"></script>
   <!-- sidebar -->
   <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="js/custom.js"></script>
   <script src="js/js.js"></script>
   <script>
      function openNav() {
         document.getElementById("myNav").style.width = "100%";
      }

      function closeNav() {
         document.getElementById("myNav").style.width = "0%";
      }
   </script>
   <!-- aktif/pasif -->
   <script>
    // Sayfa yüklendiğinde aktif yapma
    window.onload = function() {
        fetch('update_status.php', { method: 'POST' });
    };

    // Sayfa kapatıldığında pasif yapma
    window.onbeforeunload = function() {
        fetch('logout_status.php', { method: 'POST' });
    };

    // Kullanıcı aktifse (fare hareketleri, klavye etkileşimleri) status güncelleme
    var inactivityTime = function () {
        var time;
        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;

        function logout() {
            fetch('logout_status.php', { method: 'POST' });
        }

        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(logout, 30000); // 30 saniye sonra logout
        }
    };
    inactivityTime();
</script>
<!-- Back to Top Button -->
<button id="backToTop" class="back-to-top">↑</button>
<style>

/* Back to Top Butonu */
.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: opacity 0.3s, transform 0.3s;
    opacity: 0;
    transform: translateY(100px);
}

/* Butonu görünür yapacak stil */
.back-to-top.show {
    opacity: 1;
    transform: translateY(0);
}

/* Butona tıklama animasyonu */
.back-to-top:hover {
    background-color: #0056b3;
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const backToTopButton = document.getElementById('backToTop');

    // Sayfanın scroll pozisyonuna göre butonu göster
    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) { // 300px scroll mesafesinden sonra butonu göster
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });

    // Butona tıklama işlevselliği
    backToTopButton.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

</script>
</body>
</html>
