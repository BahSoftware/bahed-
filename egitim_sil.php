<?php
include_once("inc/data.php"); // Veritabanı bağlantısını sağlayan dosya

//--------------------------------Haber Category------------------------------------------------
if (isset($_GET['egitim_id'])) {
    $id = $_GET['egitim_id']; 
    $sorgu = $db->prepare("DELETE FROM bahedu   WHERE id=?");
    $sorgu->execute(array($id));
    header("location:../egitim_ekle.php");
  }
?>
