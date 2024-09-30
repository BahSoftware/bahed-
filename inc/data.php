<?php
$mysqlsunucu = "localhost";
$mysqlkullanici = "root";
$mysqlsifre = "";
try {
	$db = new PDO("mysql:host=$mysqlsunucu;dbname=bahedu;charset=utf8", $mysqlkullanici, $mysqlsifre);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Bağlantı başarılı"; 
}
catch(PDOException $e)
{
	echo "Bağlantı hatası: " . $e->getMessage();
	exit;
}

date_default_timezone_set('Europe/Istanbul');


?>