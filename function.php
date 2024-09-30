<?php
function sef_link($string) {
    // Küçük harfe çevir
    $string = strtolower($string);
    
    // Türkçe karakterleri İngilizce karşılıklarıyla değiştir
    $string = str_replace(
        array('ç', 'ı', 'ğ', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'),
        array('c', 'i', 'g', 'o', 's', 'u', 'c', 'g', 'i', 'o', 's', 'u'),
        $string
    );
    
    // Boşlukları ve özel karakterleri kaldır
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    $string = trim($string, '-');
    
    return $string;
}




function getData($db, $table, $id) {
    $query = $db->prepare("SELECT seotitle, seodescription FROM $table WHERE id = ?");
    $query->execute([$id]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    $title = $row['seotitle'] ?? "Başlık bulunamadı";
    $description = isset($row['seodescription']) ? $row['seodescription'] : "";

    return array('title' => $title, 'description' => $description);
}
?>