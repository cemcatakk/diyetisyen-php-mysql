<?php
include 'veritabani.php';
session_start();
//Sayfalar arası veri aktarımı ve aktif kullanıcı bilgisi için session kullanıyoruz
$kadi = $_POST['kadi'];
$sifre = $_POST['sifre'];
//index'ten gelen kadi ve şifre bilgisi ile veri tabanında sorgu yapılacak
if ($kadi == "admin" && $sifre == "admin") {
    //eğer veriler admin ise, sorgu yapılmadan yönetici sayfasına aktarılacak
    header('Location: yoneticipaneli.php');
    $_SESSION['aktifkullanici'] = "Admin";
    $_SESSION['girisyapildimi'] = true;
    $_SESSION['uyeturu'] = 2;
} else {
    $sql = "";
    if (isset($_POST['hastagirisi'])) {
        //Eğer hasta girişi düğmesine basıldıysa, hasta tablosunda arama yapılıyor
        $sql = "select id,kadi,sifre,diyetisyenid from hasta where kadi='$kadi' and sifre='$sifre'";
        $uyeturu = 0;
    } else if (isset($_POST['diyetisyengirisi'])) {
        //Eğer diyetisyen girişi yapıldıysa, diyetisyen tablosunda arama yapılıyor
        $sql = "select id,kadi,sifre from diyetisyen where kadi='$kadi' and sifre='$sifre'";
        $uyeturu = 1;
    } else {
        header('Location:index.php');
    }
    $result = mysqli_query($con, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        //Eşleşme bulunursa, aktif kullanıcı bilgisi ve giriş yapıldı mı gibi bilgiler true olarak değiştiriliyor
        $_SESSION['aktifkullanici'] = $kadi;
        $_SESSION['girisyapildimi'] = true;
        $_SESSION['uyeturu'] = $uyeturu;
        if (isset($_POST['hastagirisi'])) {
            $_SESSION['hastaid'] = $row['id'];
            $_SESSION['diyetisyenid'] = $row['diyetisyenid'];
        } else if (isset($_POST['diyetisyengirisi'])) {
            $_SESSION['diyetisyenid'] = $row['id'];
        }
        //Ve anasayfaya yönlendiriliyor
        header('Location: index.php');
    } else {
        $_SESSION['girisyapildimi'] = false;
        //Eğer şifre hatalı ise GET yöntemi ile anasayfaya ?hatali ekiyle gönderiliyor.
        header('Location: index.php?hatali');
    }
}
