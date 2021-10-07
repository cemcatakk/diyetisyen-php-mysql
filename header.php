<div class='icerik'>
    <a href="index.php" class="logo" style="border: none"><img src="img/logo.png" width="200px" height="150px"> </a>


    <?php
    if ($girisyapildimi) {
        //Eğer giriş yapıldı ise

        if ($uyeturu == 2) {
            //Ve üye türü 2(admin) ise
    ?>
            <a href="yoneticipaneli.php" class="menu">
                <?php
                echo $_SESSION['aktifkullanici'];
                //Yönetici paneline yönlendiren bir link oluşturuluyor
                ?>
            </a>
        <?php
        } else {
        ?>
            <a href="index.php" class="menu">
                <?php
                //Giriş yapan admin değil ise anasayfaya yönlendiren ve kullanıcının
                //kullanıcı adını alan bir link oluşturuluyor
                echo $_SESSION['aktifkullanici'];
                ?>
            </a>
        <?php
        }
        ?>


        <?php
        if ($uyeturu == 0) {
            //Eğer üye türü 0(hasta) ise aşağıdaki menüler listeleniyor
        ?>
            <a href="index.php?kaloritakip" class="menu">Kalori Takibi</a>
            <a href="index.php?egzersiztakip" class="menu">Egzersiz Takibi</a>
            <a href="index.php?sutakip" class="menu">Su Takibi</a>
            <a href="index.php?vucutdegeri" class="menu">Vucut Değerleri</a>
            <a href="index.php?mesaj" class="menu">Diyetisyene Mesaj</a>
            <a href="index.php?hesaplama" class="menu">Hesaplar</a>
        <?php
        }
        ?>
        <!-- Çıkış yap butonu-->
        <a class="menu" href="cikis.php">Çıkış Yap</a>
    <?php
    } else {
        //Eğer giriş yapılmadı ise, kullanıcı adı şifre alanları görünüyor
    ?>
        <form action="giris.php" method="POST">
            <input class="form" type="text" name="kadi" placeholder="Kullanici Adi">
            <input class="form" type="password" name="sifre" placeholder="Şifre">
            <input class="form" type="submit" name="hastagirisi" value="Hasta Girişi">
            <input class="form" type="submit" name="diyetisyengirisi" value="Diyetisyen Girişi">
        </form>
    <?php

    }
    ?>
</div>