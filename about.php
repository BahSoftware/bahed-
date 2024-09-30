<?php include("inc/header.php");?>
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
    font-size: 3rem; /* Başlık boyutunu ihtiyaca göre ayarlayın */
    font-weight: bold;
    margin: 0;
    text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7); /* Başlık gölgesi */
    color: white;
    max-width: 80%; /* Başlık genişliğini kısıtlamak için */
    line-height: 1.2; /* Satır yüksekliği */
}

.about {
        margin-top: 30px;
    }

    .carousel-inner img {
        height: 300px; /* İhtiyaca göre ayarlayın */
        object-fit: cover; /* Resimleri container'a uyacak şekilde kırp */
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0, 0, 0, 0.5); /* Buton arka plan rengi */
        border-radius: 50%; /* Daire şekli */
    }

    .about img {
        border-radius: 15px; /* Resmin köşelerini yuvarlama */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Hafif gölge efekti */
    }

    .about h2 {
        font-size: 2rem; /* Başlık boyutunu ihtiyaca göre ayarlayın */
        font-weight: 600;
        margin-bottom: 1rem;
        color: #007bff; /* Başlık rengi */
    }

    .about p {
        font-size: 1rem; /* Paragraf yazı boyutu */
        line-height: 1.6; /* Satır yüksekliği */
        margin-bottom: 1rem;
        color: #333; /* Paragraf rengi */
    }

</style>
<div class="hero-section">
    <h1>Hakkımızda</h1>
</div>


<section class="about container my-5">
    <div class="row align-items-center">
        <!-- Carousel Sol Tarafta -->
        <div class="col-md-6 mb-4 mb-md-0">
            <div id="carouselExampleIndicators" class="carousel slide">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" id="carousel-inner">
                    <!-- Resimlerin dinamik olarak ekleneceği yer -->
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Önceki</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Sonraki</span>
                </a>
            </div>
        </div>

        <!-- Yazı Sağ Tarafta -->
        <div class="col-md-6">
            <h2>Başkanın Mesajı</h2>
            <p>"Büyük Anadolu Hastaneleri olarak, sürekli güncellenen teknolojik donanımımızla, dünya çapındaki gelişmeleri takip ederek ve özellikle sizlerin desteğiyle büyüyerek, her geçen gün daha iyi hizmet sunma anlayışımızla, ülkemizin sağlık sektöründeki gelişimine katkıda bulunmaktan gurur duyuyoruz.</p>
            <p>Samsun ve Kocaeli Darıca'da toplam 45 bin m2 kapalı alana sahip iki hizmet binamız, yurtiçi ve yurtdışı kolay ulaşım sayesinde 35 farklı ülkeden hasta portföyümüz, deneyimli ve akademik hekim kadromuz ve 700'ü aşkın meslektaşımızla sağlık sektöründe marka olmak. Azimle çalışıyoruz.</p>
            <p>1997 yılında çıktığımız bu yolda vizyonumuzu ve misyonumuzu kaybetmeden aynı çizgide kalabilmenin mutluluğunu yaşarken, hizmetlerimizi daha geniş kitlelere yayma hedefiyle yeni hizmet binalarımızda yapılanmaya hız kesmeden devam ediyoruz.</p>
            <p>Sağlıkta güven sloganımızla, değerli hastalarımıza ve hasta yakınlarımıza aile sıcaklığı ve kurumsal hizmetlerimizle hem sağlığın hem de güvenin adresi olmaya devam edeceğiz."</p>
            <p><strong>Op.Dr.Yakup Yonten</strong></p>
            <p><strong>Yönetim Kurulu Başkanı</strong></p>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const images = [
            'images/hakkımızda/1.jpg',
            'images/hakkımızda/2.jpg',
            'images/hakkımızda/3.jpg',
            'images/hakkımızda/4.jpg',
            'images/hakkımızda/5.jpg',
            'images/hakkımızda/6.jpg',
            'images/hakkımızda/7.jpg',
            'images/hakkımızda/8.jpg',
            'images/hakkımızda/9.jpg',
            'images/hakkımızda/10.jpg',
            'images/hakkımızda/11.jpg',
            'images/hakkımızda/12.jpg',
            'images/hakkımızda/13.jpg',
            'images/hakkımızda/14.jpg',
            'images/hakkımızda/15.jpg',
            'images/hakkımızda/16.jpg',
            'images/hakkımızda/17.jpg',
            'images/hakkımızda/18.jpg',
            'images/hakkımızda/19.jpg',
            'images/hakkımızda/20.jpg',
            'images/hakkımızda/21.jpg',
            'images/hakkımızda/22.jpg',
            'images/hakkımızda/23.jpg',
            'images/hakkımızda/24.jpg',
            'images/hakkımızda/25.jpg',
            'images/hakkımızda/26.jpg'
            // İhtiyacınıza göre daha fazla resim ekleyin
        ];

        const carouselInner = document.getElementById('carousel-inner');

        function getRandomImage() {
            const randomIndex = Math.floor(Math.random() * images.length);
            return images[randomIndex];
        }

        function updateCarousel() {
            const items = carouselInner.querySelectorAll('.carousel-item');
            items.forEach(item => item.remove());

            for (let i = 0; i < 3; i++) {
                const item = document.createElement('div');
                item.classList.add('carousel-item');
                if (i === 0) {
                    item.classList.add('active');
                }
                const img = document.createElement('img');
                img.src = getRandomImage();
                img.classList.add('d-block', 'w-100');
                img.alt = `Hastane ${i + 1}`;
                item.appendChild(img);
                carouselInner.appendChild(item);
            }
        }

        updateCarousel();
        setInterval(updateCarousel, 3000); // 3 saniyede bir resimleri güncelle
    });
</script>

<?php include("inc/footer.php");?>
