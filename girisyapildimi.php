<?php
require 'veritabani.php';
session_start();
$girisyapildimi = false;
//Kullanıcının giriş yapıp yapmadığına göre kullanılacak $girisyapildimi değişkenini değiştiriyoruz
//Bunları sayfada kullanacağız.
if (isset($_SESSION['girisyapildimi'])) {
    if ($_SESSION['girisyapildimi']) {
        $girisyapildimi = true;
    } else
        $girisyapildimi = false;
}
