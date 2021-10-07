<!DOCTYPE html>
<html lang="en">
<?php
include 'veritabani.php';
include 'girisyapildimi.php';
$uyeturu = 2;
include 'header.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Yönetici Paneli</title>
</head>

<body>
    <?php
    //Yeni diyetisyen kaydı butonuna basıldığında, formdan gelen bilgiler
    if (isset($_POST['diyetisyenkayit'])) {
        $kadi = $_POST['kadi'];
        $sifre = $_POST['sifre'];
        $ad = $_POST['ad'];
        $soyad = $_POST['soyad'];
        //Diyetisyen tablosuna yeni bir alan olarak kaydediliyor.
        $sql = "insert into diyetisyen (kadi,sifre,ad,soyad) values('$kadi','$sifre','$ad','$soyad')";
        $con->query($sql);
        echo '<center><p class="menu width80">Kayıt Başarılı</p></center>';
    }
    ?>
    <form action="yoneticipaneli.php" method="POST">
        <center>
            <!--Yeni diyetisyen kaydı için form nesneleri bu alanda tutuluyor-->
            <p class="menu width80">Yeni Diyetisyen Kaydı</p>
            <table class="tablo2">
                <tr>
                    <th>Kullanıcı Adı</th>
                    <td><input class="menu width80" type="text" placeholder="Kullanıcı Adı" name="kadi"></td>
                </tr>
                <tr>
                    <th>Şifre</th>
                    <td><input class="menu width80" type="text" placeholder="Şifre" name="sifre"></td>
                </tr>
                <tr>
                    <th>Ad</th>
                    <td><input class="menu width80" type="text" placeholder="Adı" name="ad"></td>
                </tr>
                <tr>
                    <th>Soyad</th>
                    <td><input class="menu width80" type="text" placeholder="Soyadı" name="soyad"></td>
                </tr>
                <tr>
                    <th></th>
                    <td><input class="menu" type="submit" name="diyetisyenkayit" value="Kayıt Oluştur"></td>
                </tr>
            </table>
        </center>
    </form>
</body>

</html>