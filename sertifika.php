<?php
require('tcpdf/TCPDF-main/tcpdf.php'); // TCPDF kütüphanesini dahil edin

// Yeni bir PDF belgesi oluşturun
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Sayfa başlığı
$pdf->SetTitle('Büyük Anadolu Hastaneleri Eğitim Sertifikası');

// Arka plan rengi
$pdf->SetFillColor(240, 240, 240); // Açık gri arka plan
$pdf->Rect(0, 0, 210, 297, 'F'); // Sayfa boyutları

// Dalgalı çizgi fonksiyonu
function drawWavyLine($pdf, $x1, $y1, $x2, $y2) {
    $waveHeight = 5; // Dalga yüksekliği
    $frequency = 2; // Dalga sıklığı
    $pdf->SetLineWidth(0.5);
    $pdf->SetDrawColor(0, 0, 255); // Mavi çizgi rengi

    $segmentLength = 5; // Dalga segment uzunluğu
    for ($x = $x1; $x < $x2; $x += $segmentLength) {
        $yOffset = $waveHeight * sin($frequency * ($x - $x1) * 0.1);
        $pdf->Line($x, $y1 + $yOffset, $x + $segmentLength, $y1 + $yOffset);
    }
}

// Sayfa ekleme
$pdf->AddPage(); // Sayfa eklemeden önce `setPage()` fonksiyonunu çağırmayın

// Logo ekleme
$logoPath = 'images/icon.png'; // Logo dosyasının yolu
$pdf->Image($logoPath, 10, 10, 50, 20, 'PNG'); // Logo konumu ve boyutu

// Başlık ve metin ayarları
$pdf->SetFont('dejavusans', 'B', 20); // Büyük başlık fontu
$pdf->SetXY(70, 25); // Başlık konumu
$pdf->Cell(0, 10, 'Büyük Anadolu Hastaneleri', 0, 1, 'C');
$pdf->SetFont('dejavusans', 'B', 16); // Alt başlık fontu
$pdf->Cell(0, 10, 'Eğitim Sertifikası', 0, 1, 'C');

// Dalgalı çizgiler ekleme
drawWavyLine($pdf, 10, 40, 200, 40); // Üst dalgalı çizgi
drawWavyLine($pdf, 10, 260, 200, 260); // Alt dalgalı çizgi

// Sertifika içeriği
$pdf->SetFont('dejavusans', '', 14); // Sertifika içeriği fontu
$pdf->SetXY(10, 50); // Başlığın altına konumlandırma
$name = isset($_POST['name']) ? $_POST['name'] : 'Katılımcı';
$pdf->MultiCell(0, 10, "Bu sertifika, {$name}'na verilmiştir.", 0, 'C');

// Alt bilgi
$pdf->SetY(-40); // Alt kısmı ayarlamak için
$pdf->SetFont('dejavusans', 'I', 12);
$pdf->Cell(0, 10, 'Tarih: ' . date('d-m-Y'), 0, 1, 'C');

// Sertifikayı PDF olarak çıkartın ve tarayıcıya gönderin
$pdf->Output('sertifika.pdf', 'I'); // 'I' parametresi PDF'i tarayıcıda görüntüler
?>
