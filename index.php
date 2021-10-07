<?php
include 'veritabani.php';
include 'girisyapildimi.php';
$uyeturu = -1;

$hastaid = -1;
$diyetisyenid = -1;
//Başlangıçte 3 değişkene de -1 değerini veriyoruz, ileride sorgulara göre dolduracağız.
if (isset($_GET['hatali'])) {
    echo '<center><p class="menu width80" style="background-color:red">Kullanıcı Adı veya Şifre Hatalı!</p></center>';
} //Giriş hatalı ise kullanıcıyı bilgilendiriyoruz

if (isset($_SESSION['uyeturu'])) {
    //Üye türü bilgisi girilmiş ise, yukarıda tanımladığımız $uyeturu değişkenine göre
    //Diğer diyetisyenid ve hastaid gibi sık kullanılacak değişkenleri belirliyoruz
    $uyeturu = $_SESSION['uyeturu'];
    if ($uyeturu == 0) {
        $hastaid = $_SESSION['hastaid'];
        $diyetisyenid = $_SESSION['diyetisyenid'];
    } else if ($uyeturu == 1) {
        $diyetisyenid = $_SESSION['diyetisyenid'];
    }
}
include 'header.php';

function yemeklistele($con, $yemekno)
//Yemekler tablosundaki tüm yemekleri bir select içinde listeleyen fonksiyon
{
    $sql = "select * from yemekler";
    $result = mysqli_query($con, $sql);
    echo '<tr><td><select name=' . $yemekno . '>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option>' . $row['yemekadi'] . '</option>';
    }
    echo '</select></td></tr>';
}
function iceceklistele($con)
{ //Aynı işlemi içecekler için yapıyoruz
    $sql = "select * from icecekler";
    $result = mysqli_query($con, $sql);
    echo '<tr><td><select name="icecek">';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option>' . $row['icecekadi'] . '</option>';
    }
    echo '</select></td></tr>';
}

function diyetlistele($diyetogun, $con, $hastaid)
{
    //Kullanıcının diyetisyeni tarafından gönderilmiş diyetlerini listelediğimiz fonksiyon
    //Parametre olarak aldığı öğün ve hastaid bilgisine göre diyetleri listeleniyor
    $sql = "select $diyetogun from diyetlistesi where hastaid=$hastaid";
    $result = mysqli_query($con, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $toplam = 0;
        $sql = "select * from diyetler where id=" . $row[$diyetogun];
        $result = mysqli_query($con, $sql);
        if ($row2 = mysqli_fetch_assoc($result)) {
            $sql = "select * from yemekler where id=" . $row2['yemek1id'];
            $result = mysqli_query($con, $sql);
            if ($row3 = mysqli_fetch_assoc($result)) {
                echo '<tr> <td> ' . $row3['yemekadi'] . '</td>
                                  <td>' . $row3['kalori'] . '</td></tr>';
                $toplam += $row3['kalori'];
            }
            $sql = "select * from yemekler where id=" . $row2['yemek2id'];
            $result = mysqli_query($con, $sql);
            if ($row3 = mysqli_fetch_assoc($result)) {
                echo '<tr> <td> ' . $row3['yemekadi'] . '</td>
                                  <td>' . $row3['kalori'] . '</td></tr>';
                $toplam += $row3['kalori'];
            }
            $sql = "select * from yemekler where id=" . $row2['yemek3id'];
            $result = mysqli_query($con, $sql);
            if ($row3 = mysqli_fetch_assoc($result)) {
                echo '<tr> <td> ' . $row3['yemekadi'] . '</td>
                                  <td>' . $row3['kalori'] . '</td></tr>';
                $toplam += $row3['kalori'];
            }
            $sql = "select * from icecekler where id=" . $row2['icecekid'];
            $result = mysqli_query($con, $sql);
            if ($row3 = mysqli_fetch_assoc($result)) {
                echo '<tr> <td> ' . $row3['icecekadi'] . '</td>
                                  <td>' . $row3['kalori'] . '</td></tr>';
                $toplam += $row3['kalori'];
            }
        }
        echo '<tr><th></th> <th>Toplam: ' . $toplam . '</th></tr>';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Diyet Takip Sistemi</title>
</head>

<body>
    <?php
    if ($uyeturu == 0) {
        //Hasta işlemleri
        //Hastanın diyet listesi listeleniyor
    ?>
        <div class="tablolar">
            <div class="tablo">
                <table>
                    Kahvaltı
                    <tr>
                        <th>
                            Yemeniz Gereken
                        </th>
                        <th>Kalori</th>
                    </tr>
                    <?php
                    diyetlistele('sabahdiyetiid', $con, $hastaid);
                    ?>
                </table>
            </div>
            <div class="tablo">
                <table>
                    Öğle Yemeği
                    <tr>
                        <th>
                            Yemeniz Gereken
                        </th>
                        <th>Kalori</th>
                    </tr>
                    <?php
                    diyetlistele('oglendiyetiid', $con, $hastaid);
                    ?>
                </table>
            </div>
            <div class="tablo">
                <table>
                    Ara Öğün
                    <tr>
                        <th>
                            Yemeniz Gereken
                        </th>
                        <th>Kalori</th>
                    </tr>
                    <?php
                    diyetlistele('atistirmalikdiyetid', $con, $hastaid);
                    ?>
                </table>
            </div>
            <div class="tablo">
                Akşam Yemeği
                <table>
                    <tr>
                        <th>
                            Yemeniz Gereken
                        </th>
                        <th>Kalori</th>
                    </tr>
                    <?php
                    diyetlistele('aksamdiyetiid', $con, $hastaid);
                    ?>
                </table>
            </div>
        </div>

        <?php
        if (isset($_GET['kaloritakip'])) {
            //Kalori takip sayfasına tıklanmış ise
            //Ve kahvaltı, öyle yemeği gönder gibi 4 butondan biri tıklanmış ise;
            if (isset($_POST['kahvaltigonder']) || isset($_POST['ogleyemegigonder']) || isset($_POST['araogungonder']) || isset($_POST['aksamyemegigonder'])) {
                $yemek1 = $_POST['yemek1'];
                $yemek2 = $_POST['yemek2'];
                $yemek3 = $_POST['yemek3'];
                $icecek = $_POST['icecek'];
                //3 yemek ve bir içecek bilgisini değişkene aktarıyoruz
                //Ardından hangi öğün gönderildiyse veritabanında gönderilen yemekler tablosuna yeni veri olarak ekliyoruz
                //Bu bilgi diyetisyen sayfasına gidiyor
                $sql = "";
                if (isset($_POST['kahvaltigonder'])) {
                    $sql = "insert into gonderilenyemekler (hastaid,diyetisyenid,ogun,yemek1ad,yemek2ad,yemek3ad,icecekad) values($hastaid,$diyetisyenid,'Kahvalti','$yemek1','$yemek2','$yemek3','$icecek')";
                    echo '<center><p class="menu">Kahvaltı Öğününüz Diyetisyeninize Gönderildi.</p></center>';
                } else if (isset($_POST['ogleyemegigonder'])) {
                    $sql = "insert into gonderilenyemekler (hastaid,diyetisyenid,ogun,yemek1ad,yemek2ad,yemek3ad,icecekad) values($hastaid,$diyetisyenid,'Öğle Yemeği','$yemek1','$yemek2','$yemek3','$icecek')";
                    echo '<center><p class="menu">Öğle Yemeği Öğününüz Diyetisyeninize Gönderildi.</p></center>';
                } else if (isset($_POST['araogungonder'])) {
                    $sql = "insert into gonderilenyemekler (hastaid,diyetisyenid,ogun,yemek1ad,yemek2ad,yemek3ad,icecekad) values($hastaid,$diyetisyenid,'Ara Öğün','$yemek1','$yemek2','$yemek3','$icecek')";
                    echo '<center><p class="menu">Ara Öğününüz Diyetisyeninize Gönderildi.</p></center>';
                } else if (isset($_POST['aksamyemegigonder'])) {
                    $sql = "insert into gonderilenyemekler (hastaid,diyetisyenid,ogun,yemek1ad,yemek2ad,yemek3ad,icecekad) values($hastaid,$diyetisyenid,'Akşam Yemeği','$yemek1','$yemek2','$yemek3','$icecek')";
                    echo '<center><p class="menu">Akşam Yemeği Öğününüz Diyetisyeninize Gönderildi.</p></center>';
                }
                if ($con->query($sql) == false) {
                    echo "hata: " . $con->error;
                }
            }

        ?>
            <br>
            <div class="tablolar">


                <table class="tablo">
                    <form action="index.php?kaloritakip" method="POST">
                        <tr>
                            <th>Kahvaltı Öğününüz</th>
                        </tr>
                        <?php
                        //Yediğimiz öğünleri seçip diyetisyene göndermemizi sağlayan alanlar, aşağıdaki 3 işlem de aynı şekilde
                        yemeklistele($con, 'yemek1');
                        yemeklistele($con, 'yemek2');
                        yemeklistele($con, 'yemek3');
                        iceceklistele($con);
                        ?>
                        <tr>
                            <td><input class="menu" type="submit" name="kahvaltigonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>
                <table class="tablo">
                    <form action="index.php?kaloritakip" method="POST">
                        <tr>
                            <th>Öğle Yemeği Öğününüz</th>
                        </tr>
                        <?php
                        yemeklistele($con, 'yemek1');
                        yemeklistele($con, 'yemek2');
                        yemeklistele($con, 'yemek3');
                        iceceklistele($con);
                        ?>
                        <tr>
                            <td><input class="menu" type="submit" name="ogleyemegigonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>
                <table class="tablo">
                    <form action="index.php?kaloritakip" method="POST">
                        <tr>
                            <th>Atıştırmalık Öğününüz</th>
                        </tr>
                        <?php
                        yemeklistele($con, 'yemek1');
                        yemeklistele($con, 'yemek2');
                        yemeklistele($con, 'yemek3');
                        iceceklistele($con);
                        ?>
                        <tr>
                            <td><input class="menu" type="submit" name="araogungonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>

                <table class="tablo">
                    <form action="index.php?kaloritakip" method="POST">
                        <tr>
                            <th>Akşam Yemeği Öğününüz</th>
                        </tr>
                        <?php
                        yemeklistele($con, 'yemek1');
                        yemeklistele($con, 'yemek2');
                        yemeklistele($con, 'yemek3');
                        iceceklistele($con);
                        ?>
                        <tr>
                            <td><input class="menu" type="submit" name="aksamyemegigonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>

            </div>


        <?php
        } else if (isset($_GET['egzersiztakip'])) {
            //Egzersiz takip sayfasına tıklandı ise
            if (isset($_POST['egzersizgonder'])) {
                //Eğer egzersiz bilgisi diyetisyene gönderilecek ise
                $egzersizadi = $_POST['egzersizadi'];
                $egzersizsuresi = $_POST['egzersizsuresi'];
                //Alınan verilere göre veri tabanında insert sorgusu gerçekleştiriliyor
                $sql = "insert into egzersiz (hastaid,diyetisyenid,yapilanegzersiz,yapilansure) values($hastaid,$diyetisyenid,'$egzersizadi',$egzersizsuresi)";
                if ($con->query($sql) == true) {
                    echo '<center><p class="menu">Egzersiz Bilginiz Diyetisyeninize Gönderildi.</p></center>';
                }
                echo "Hata: " . $con->error;
            }
        ?>
            <br>
            <center>
                <!-- Yapılan egzersiz formu-->
                <table class="tablo">
                    <form action="index.php?egzersiztakip" method="POST">
                        <tr>
                            <th>Yaptığınız Egzersiz</th>
                            <th>Yapılan Süre(dk)</th>
                        </tr>
                        <tr>
                            <td><input class="menu" name="egzersizadi" type="text"></td>
                            <td><input class="menu" name="egzersizsuresi" type="number"></td>
                            <td><input class="menu" type="submit" name="egzersizgonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>
            </center>
        <?php
        } else if (isset($_GET['sutakip'])) {
            //Girilen su bilgisini diyetisyene göndermek için;
            if (isset($_POST['sugonder'])) {
                //Su Bilgisi gönder butonuna basıldı ise, kaç adet bardak su içildiğini
                //icilensu tablosuna ekliyoruz
                $suadet = $_POST['suadet'];
                $sql = "insert into icilensu (hastaid,diyetisyenid,icilensu,tarih) values($hastaid,$diyetisyenid,$suadet,CURDATE())";
                if ($con->query($sql) == true) {
                    echo '<center><p class="menu">İçtiğiniz Su Bilgisi Diyetisyeninize Gönderildi.</p></center>';
                }
                echo "Hata: " . $con->error;
            }
        ?>
            <br>
            <center>
                <table class="tablo">
                    <form action="index.php?sutakip" method="POST">
                        <tr>
                            <th>İçilen Bardak Su (200ml)</th>
                        </tr>
                        <tr>
                            <td><input class="menu" name="suadet" type="number"></td>
                            <td><input class="menu" type="submit" name="sugonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>
            </center>
        <?php
        } else if (isset($_GET['vucutdegeri'])) {
            //Vücut değeri sayfasına geçildi ise;
            if (isset($_POST['vucutdegerigonder'])) {
                $agirlik = $_POST['agirlik'];
                $yagorani = $_POST['yagorani'];
                $kaskutlesi = $_POST['kaskutlesi'];
                $belolcusu = $_POST['belolcusu'];
                $kalcaolcusu = $_POST['kalcaolcusu'];
                $gogusolcusu = $_POST['gogusolcusu'];
                $uylukolcusu = $_POST['uylukolcusu'];
                $kololcusu = $_POST['kololcusu'];
                //Formdan gelen verileri vucutdegeri tablosuna ekliyoruz
                $sql = "insert into vucutdegeri(hastaid,diyetisyenid,tarih,agirlik,yagorani,kasorani,kalcaolcusu,belolcusu,gogusolcusu,uylukolcusu,kololcusu) values($hastaid,$diyetisyenid,CURDATE(),$agirlik,$yagorani,$kaskutlesi,$kalcaolcusu,$belolcusu,$gogusolcusu,$uylukolcusu,$kololcusu)";
                if ($con->query($sql) == true) {
                    echo '<center><p class="menu">Vücut Değerleriniz Diyetisyeninize Gönderildi.</p></center>';
                }
                echo "Hata: " . $con->error;
            }
        ?>
            <br>
            <center>
                <!-- Vücut Değerlerinin girildiği form-->
                <table class="tablo">
                    <form action="index.php?vucutdegeri" method="POST">
                        <tr>
                            <th>Vücut Ağırlığı (kg)</th>
                            <td><input class="menu" name="agirlik" type="number"></td>
                        </tr>
                        <tr>
                            <th>Vücut Yağ Oranı (%)</th>
                            <td><input class="menu" name="yagorani" type="number"></td>
                        </tr>
                        <tr>
                            <th>Kas Kütlesi (%)</th>
                            <td><input class="menu" name="kaskutlesi" type="number"></td>
                        </tr>
                        <tr>
                            <th>Bel Ölçüsü (cm)</th>
                            <td><input class="menu" name="belolcusu" type="number"></td>
                        </tr>
                        <tr>
                            <th>Kalça Ölçüsü (cm)</th>
                            <td><input class="menu" name="kalcaolcusu" type="number"></td>
                        </tr>
                        <tr>
                            <th>Göğüs Ölçüsü (cm)</th>
                            <td><input class="menu" name="gogusolcusu" type="number"></td>
                        </tr>
                        <tr>
                            <th>Uyluk Ölçüsü (cm)</th>
                            <td><input class="menu" name="uylukolcusu" type="number"></td>
                        </tr>
                        <tr>
                            <th>Kol Ölçüsü (cm)</th>
                            <td><input class="menu" name="kololcusu" type="number"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input class="menu" type="submit" name="vucutdegerigonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>
            </center>
            <br><br><br><br>
        <?php
        } else if (isset($_GET['hesaplama'])) {
            //Hesaplama sayfası:
            $cinsiyet = "";
            $boy;
            $kilo;
            $yas;
            //Önce kullanıcının yaş bilgisi hesaplanıyor ve diğer boy kilo gibi bilgileri alınıyor
            $sql = "select cinsiyet,boy,kilo,YEAR(CURDATE()) - YEAR(dtarihi) AS yas from hasta where id=$hastaid";
            $result = mysqli_query($con, $sql);

            if ($row = mysqli_fetch_assoc($result)) {
                $cinsiyet = $row['cinsiyet'];
                $boy = $row['boy'];
                $kilo = $row['kilo'];
                $yas = $row['yas'];
            }
            //Ardından cinsiyetine göre bazal met. hızı ve vki'si hesaplanılıyor
            $bazalformul;

            $boy = substr_replace($boy, '.', 1, 0);
            $vki = $kilo / ($boy * $boy);
            if ($cinsiyet == 'Kadın') {
                $bazalformul = (655 + (9.6 * $kilo) + (1.8 * $boy) - (4.7 * $yas));
            } else {
                $bazalformul = (66.5 + (13.7 * $kilo) + (5 * $boy) - (6.7 * $yas));
            }
            if (isset($_POST['hesapkaydet'])) { //İPTAL EDİLDİ
            }
        ?>
            <br>
            <center>
                <!-- Bazal metabolizma bilgilerinin göründüğü form; iframe içerisinde dış siteden çektiğimiz yağ oranı ölçer var -->
                <div class="tablolar">
                    <iframe class="menu" width="400" height="520" src="https://www.diyetasistan.com/vucut-yag-orani-formu-embed.html" frameborder="0" allowfullscreen></iframe>
                    <table class="tablo2">
                        <form action="index.php?hesaplama" method="POST">
                            <tr>
                                <th>Bazal Metabolizma Hızınız</th>
                                <td>
                                    <p class="menu"><?php echo $bazalformul ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th>Vücut Kitle İndeksiniz</th>
                                <td>
                                    <p class="menu"><?php echo $vki ?></p>
                                </td>
                            </tr>
                        </form>
                    </table>
                </div>

            </center>

        <?php
        } else if (isset($_GET['mesaj'])) {
            //Mesajlaşma sayfası

        ?>
            <br>

            <?php
            //Hasta ve diyetisyen arasında geçen tüm mesajlar listelenecek
            //Öncesinde diyetisyenin adı soyadı bilgisi veri tabanından alınıyor
            $sql = "select ad,soyad from diyetisyen where id=$diyetisyenid";
            $result = mysqli_query($con, $sql);
            $diyetisyenadsoyad;
            if ($row = mysqli_fetch_assoc($result)) {
                $diyetisyenadsoyad = $row['ad'] . " " . $row['soyad'];
            }
            //Ardından hastanın ad soyad bilgisi alınıyor
            //Bu bilgiler mesaj panosunda solda görüntülenecek
            $sql = "select ad,soyad from hasta where id=$hastaid";
            $result = mysqli_query($con, $sql);
            $hastaadsoyad;
            if ($row = mysqli_fetch_assoc($result)) {
                $hastaadsoyad = $row['ad'] . " " . $row['soyad'];
            }


            if (isset($_POST['mesajgonder'])) {
                $mesaj = $_POST['mesaj'];
                //Mesaj gönder butonuna basılmışsa, formdan gelen verilere göre mesajlar tablosuna yeni veri ekleniyor
                $sql = "insert into mesajlar(diyetisyenid,hastaid,mesaj,tarih,gonderen) values($diyetisyenid,$hastaid,'$mesaj',NOW(),'$hastaadsoyad')";
            }
            $con->query($sql);
            //Ve mesajlar listeleniyor


            $sql = "select * from (select * from mesajlar where diyetisyenid=$diyetisyenid and hastaid=$hastaid ORDER BY id desc LIMIT 5)sub order by id asc";


            $result = mysqli_query($con, $sql);
            ?>

            <center>
                <!-- Mesajların listelendiği ve gönderildiği form-->
                <table class="mesajtablo">
                    <th>Gönderen</th>
                    <th>Mesaj</th>
                    <th>Gönderim Tarihi</th>
                    </tr>

                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr >';
                        echo '<td class="bilgitd">' . $row['gonderen'] . '</td>';
                        echo '<td class="mesajtd">' . $row['mesaj'] . '</td>';
                        echo '<td class="bilgitd">' . $row['tarih'] . '</td>';
                        echo '</tr>';
                        //Her bir mesaj veri tabanında gelen veriye göre tr içinde listeleniyor
                    }
                    ?>
                    <form action="index.php?mesaj" method="POST">
                        <tr>
                            <th>Mesajınız</th>
                            <td><textarea class="menu" name="mesaj" cols="90" rows="5"></textarea></td>
                            <td><input class="menu" type="submit" name="mesajgonder" value="Gönder"></td>
                        </tr>
                    </form>
                </table>
            </center>
        <?php
        }
    } else if ($uyeturu == 1) {
        //Diyetisyen sayfası
        if (isset($_POST['yenikayit'])) {
            //Yeni kayıt butonuna basıldı ise formdan gelen veriler alınıyor
            $kadi = $_POST['kadi'];
            $sifre = $_POST['sifre'];
            $ad = $_POST['ad'];
            $soyad = $_POST['soyad'];
            $dtarihi = $_POST['dtarihi'];
            $dtarihi = date("Y-m-d", strtotime($dtarihi));
            echo $dtarihi;
            $cinsiyet = $_POST['cinsiyet'];
            $meslek = $_POST['meslek'];
            $telefon = $_POST['tel'];
            $eposta = $_POST['eposta'];
            $boy = $_POST['boy'];
            $kilo = $_POST['kilo'];
            $hedefkilo = $_POST['hedefkilo'];
            $istek = $_POST['istek'];
            //Ve bu bilgiler ile hasta tablosuna yeni bir kayıt yapılıyor
            $sql = "INSERT INTO `hasta`(`diyetisyenid`, `kadi`, `sifre`, `ad`, `soyad`, `dtarihi`, `cinsiyet`, `meslek`, `tel`, `eposta`, `boy`, `kilo`, `hedefkilo`, `istek`)
             VALUES ($diyetisyenid,'$kadi','$sifre','$ad','$soyad','$dtarihi','$cinsiyet','$meslek','$telefon','$eposta',$boy,$kilo,$hedefkilo,'$istek')";
            if ($con->query($sql) == true) {
                echo '<p class="menu">Yeni Kayıt Başarılı</p>';
            } else {
                echo "Hata: " . $con->error;
            }
        }
        //Diyetisyen
        ?>
        <center>
            <!-- Hasta listesi -->
            <a href="index.php" class="menu width80">Hasta Listesi</a>
            <a href="index.php?hastaekle" class="menu width80">Hasta Kaydı Ekle</a>
            <table class="tablo2 width90">
                <tr>
                    <th>Hasta ID</th>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>Doğum Tarihi</th>
                    <th>Cinsiyet</th>
                    <th>Meslek</th>
                    <th>Telefon</th>
                    <th>E-Posta</th>
                    <th>Boy</th>
                    <th>Kilo</th>
                    <th>Hedef Kilo</th>
                    <th>Amacı</th>
                </tr>
                <?php
                $sql = "select * from hasta where diyetisyenid=$diyetisyenid";
                //Veri tabanında tüm hasta bilgileri alınıyor, koşul olarak giriş yapan diyetisyenin id'sine sahip olanlar getiriliyor
                $result = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    $loc = "index.php?hastabilgi_" . $row['id'];
                    echo '<tr class="menu trlink">';
                    //Ardından gelen veriler uygun bir formatta listeleniyor
                    echo '<td><a href=' . $loc . '>' . $row['id'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['ad'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['soyad'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['dtarihi'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['cinsiyet'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['meslek'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['tel'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['eposta'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['boy'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['kilo'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['hedefkilo'] . '</a></td>';
                    echo '<td><a href=' . $loc . '>' . $row['istek'] . '</a></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </center>
        <br>
        <center>
            <?php
            $sql = "select * from hasta";
            //Bu alanda hastanın egzersiz bilgileri listeleniyor
            //Yaptığı egzersize göre diyetisyen yaktığı kalori bilgisini girebiliyor.
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['id'];
                if (isset($_GET['hastabilgi_' . $row['id']])) {

                    $sql = "select * from egzersiz";
                    $result = mysqli_query($con, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        if (isset($_POST[$row['id']])) {
                            $kalorimiktari = $_POST['kalorimiktari' . $row['id']];
                            //Veriler arasından hangi egzersizin güncelle butonuna basıldıysa bulunuyor
                            //Ve buna göre veri tabanındaki o satır güncelleniyor
                            $sql = "update egzersiz set yakilankalori=$kalorimiktari where id=" . $row['id'];
                            $con->query($sql);
                        }
                    }

            ?>
                    <div class="tablolar">
                        <table class="tablo2">
                            <form action="index.php?hastabilgi_<?php echo $id; ?>" method="POST">
                                <tr>
                                    <th>Yaptığı Egzersiz</th>
                                    <th>Süre</th>
                                    <th>Yaktığı Kalori</th>
                                </tr>

                                <?php
                                $sql = "select * from egzersiz where hastaid=$id and diyetisyenid=$diyetisyenid";
                                $result = mysqli_query($con, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    //Hastanın yaptığı egzersizler listeleniyorken
                                    //Sonundaki güncelle butonunun ismi, hasta idsine göre veriliyor
                                    //Böylece teker teker güncelleme işlemi yapabiliyoruz
                                    echo '<tr>';
                                    echo '<td>' . $row['yapilanegzersiz'] . '</td>'; //Yaptığı
                                    echo '<td>' . $row['yapilansure'] . '</td>'; //Yaptığı süre
                                    echo '<td><input type="number" value="' . $row['yakilankalori'] . '" name="kalorimiktari' . $row['id'] . '" class="menu"></td>';
                                    //
                                    echo '<td><input type="submit" class="menu" name="' . $row['id'] . '" value="Güncelle"></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </form>
                        </table>
                        <table class="tablo">
                            <tr>
                                <th>İçtiği Bardak Su</th>
                                <th>Tarih</th>
                            </tr>
                            <?php
                            $sql = "select * from icilensu where hastaid=$id and diyetisyenid=$diyetisyenid";
                            //İçilen su tablosundaki veriler listeleniyor
                            $result = mysqli_query($con, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['icilensu'] . '</td>';
                                echo '<td>' . $row['tarih'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </table>

                    </div>
                    <br>
                    <table class="tablo3">
                        <tr>
                            <th>Agirlik</th>
                            <th>Yağ Oranı</th>
                            <th>Kas Oranı</th>
                            <th>Kalça Ölçüsü</th>
                            <th>Bel Ölçüsü</th>
                            <th>Göğüs Ölçüsü</th>
                            <th>Uyluk Ölçüsü</th>
                            <th>Kol Ölçüsü</th>
                            <th>Tarih</th>
                        </tr>
                        <?php
                        $sql = "select * from vucutdegeri where hastaid=$id and diyetisyenid=$diyetisyenid";
                        //Hastanın vücut değerleri listeleniyor
                        $result = mysqli_query($con, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $row['agirlik'] . 'kg</td>';
                            echo '<td>' . $row['yagorani'] . '%</td>';
                            echo '<td>' . $row['kasorani'] . '%</td>';
                            echo '<td>' . $row['kalcaolcusu'] . 'cm</td>';
                            echo '<td>' . $row['belolcusu'] . 'cm</td>';
                            echo '<td>' . $row['gogusolcusu'] . 'cm</td>';
                            echo '<td>' . $row['uylukolcusu'] . 'cm</td>';
                            echo '<td>' . $row['kololcusu'] . 'cm</td>';
                            echo '<td>' . $row['tarih'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                    <br>
                    <?php

                    $sql = "select ad,soyad from diyetisyen where id=$diyetisyenid";
                    //Kullanıcı tarafında yapılan mesaj işleminin aynısı
                    $result = mysqli_query($con, $sql);
                    $diyetisyenadsoyad;
                    if ($row = mysqli_fetch_assoc($result)) {
                        $diyetisyenadsoyad = $row['ad'] . " " . $row['soyad'];
                    }
                    if (isset($_POST['mesajgonder'])) {
                        $mesaj = $_POST['mesaj'];
                        $sql = "insert into mesajlar(diyetisyenid,hastaid,mesaj,tarih,gonderen) values($diyetisyenid,$id,'$mesaj',NOW(),'$diyetisyenadsoyad')";
                    }
                    $con->query($sql);
                    $sql = "select * from (select * from mesajlar where diyetisyenid=$diyetisyenid and hastaid=$id ORDER BY id desc LIMIT 5)sub order by id asc";
                    $result = mysqli_query($con, $sql);
                    ?>
                    <center>
                        <table class="mesajtablo">
                            <th>Gönderen</th>
                            <th>Mesaj</th>
                            <th>Gönderim Tarihi</th>
                            </tr>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr >';
                                echo '<td class="bilgitd">' . $row['gonderen'] . '</td>';
                                echo '<td class="mesajtd">' . $row['mesaj'] . '</td>';
                                echo '<td class="bilgitd">' . $row['tarih'] . '</td>';
                                echo '</tr>';
                            }
                            if (isset($_POST['diyetkaydet'])) {
                                //Hastaya yeni diyet göndermek için kullanılan alan;
                                //Diyet kaydet butonuna basıldı ise formdan öğün, yemek ve kalori bilgileri alınıyor
                                $ogunadi = $_POST['ogun'];



                                $yemek1adi = $_POST['yemek1'];
                                $yemek1kalori = $_POST['yemek1kalori'];
                                $sql = "insert into yemekler (yemekadi,kalori) values('$yemek1adi',$yemek1kalori)";
                                $con->query($sql);
                                //Bu yemek bilgileri önce yemekler listesine ekleniyor
                                $yemek1id;
                                $sql = "select LAST_INSERT_ID() as id";
                                //Ardından son eklenen yemeğin ID'si alınıyor 
                                $result = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result)) {
                                    //Ve buler değişkende tutuluyor
                                    $yemek1id = $row['id'];
                                }

                                $yemek2adi = $_POST['yemek2'];
                                $yemek2kalori = $_POST['yemek2kalori'];
                                $sql = "insert into yemekler (yemekadi,kalori) values('$yemek2adi',$yemek2kalori)";
                                $con->query($sql);
                                $yemek2id;
                                $sql = "select LAST_INSERT_ID() as id";
                                $result = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $yemek2id = $row['id'];
                                }

                                $yemek3adi = $_POST['yemek3'];
                                $yemek3kalori = $_POST['yemek3kalori'];
                                $sql = "insert into yemekler (yemekadi,kalori) values('$yemek3adi',$yemek3kalori)";
                                $con->query($sql);
                                $yemek3id;
                                $sql = "select LAST_INSERT_ID() as id";
                                $result = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $yemek3id = $row['id'];
                                }

                                $icecekadi = $_POST['icecek'];
                                $icecekkalori = $_POST['icecekkalori'];
                                $sql = "insert into icecekler (icecekadi,kalori) values('$icecekadi',$icecekkalori)";
                                $con->query($sql);
                                $icecekid;
                                $sql = "select LAST_INSERT_ID() as id";
                                $result = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $icecekid = $row['id'];
                                }




                                //3 yemek ve içecekte bu işlemler yapıldıktan sonra
                                //Diyetler tablosuna bu yemeklerin ID'sine ekleniyor
                                $sql = "insert into diyetler(ogun,yemek1id,yemek2id,yemek3id,icecekid) values('$ogunadi',$yemek1id,$yemek2id,$yemek3id,$icecekid)";
                                if ($con->query($sql) == false) {
                                    echo "hata:" . $con->error;
                                }

                                $sql = "select LAST_INSERT_ID() as id";

                                $diyetid;
                                $result = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $diyetid = $row['id'];
                                }
                                //Ardından kullanıcının daha önce bir diyet listesi olup olmadığı sorgulanıyor 
                                //Var ise o öğünün diyeti güncelleniyor
                                //Yok ise o hasta için yeni bir diyet listesi oluşturuluyor
                                $sql = "select * from diyetlistesi where diyetisyenid=$diyetisyenid and hastaid=$id";
                                $result = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $sql = "select * from diyetlistesi where diyetisyenid=$diyetisyenid and hastaid=$id";
                                    $result = mysqli_query($con, $sql);
                                    if ($row = mysqli_fetch_assoc($result)) {
                                        if ($ogunadi == "Kahvaltı") {
                                            $sql = "update diyetlistesi set sabahdiyetiid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        } else if ($ogunadi == "Öğle Yemeği") {
                                            $sql = "update diyetlistesi set oglendiyetiid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        } else if ($ogunadi == "Ara Öğün") {
                                            $sql = "update diyetlistesi set atistirmalikdiyetid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        } else {
                                            $sql = "update diyetlistesi set aksamdiyetiid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        }
                                    }
                                } else {
                                    $sql = "insert into diyetlistesi (diyetisyenid,hastaid,sabahdiyetiid,oglendiyetiid,aksamdiyetiid,atistirmalikdiyetid) values($diyetisyenid,$id,0,0,0,0)";
                                    $con->query($sql);
                                    $sql = "select * from diyetlistesi where diyetisyenid=$diyetisyenid and hastaid=$id";
                                    $result = mysqli_query($con, $sql);
                                    if ($row = mysqli_fetch_assoc($result)) {
                                        if ($ogunadi == "Kahvaltı") {
                                            $sql = "update diyetlistesi set sabahdiyetiid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        } else if ($ogunadi == "Öğle Yemeği") {
                                            $sql = "update diyetlistesi set oglendiyetiid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        } else if ($ogunadi == "Ara Öğün") {
                                            $sql = "update diyetlistesi set atistirmalikdiyetid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        } else {
                                            $sql = "update diyetlistesi set aksamdiyetiid=$diyetid where hastaid=$id and diyetisyenid=$diyetisyenid";
                                            $con->query($sql);
                                        }
                                    }
                                }
                            }
                            ?>
                            <form action="index.php?hastabilgi_<?php echo $id ?>" method="POST">
                                <tr>
                                    <th>Mesajınız</th>
                                    <td><textarea class="menu" name="mesaj" cols="90" rows="5"></textarea></td>
                                    <td><input class="menu" type="submit" name="mesajgonder" value="Gönder"></td>
                                </tr>
                            </form>
                        </table>
                        <br>
                        <p class="menu width80">Hasta Diyeti</p>
                        <table class="tablo2">
                            <form action="index.php?hastabilgi_<?php echo $id; ?>" method="POST">
                                <tr>
                                    <th>Öğün</th>
                                    <td><select style="width: 150px" name="ogun">
                                            <option value="Kahvaltı">Kahvaltı</option>
                                            <option value="Öğle Yemeği">Öğle Yemeği</option>
                                            <option value="Ara Öğün">Ara Öğün</option>
                                            <option value="Akşam Yemeği">Akşam Yemeği</option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <th>Yemek 1</th>
                                    <td><input type="text" class="menu width90" name="yemek1"></td>
                                    <th>Kalori</th>
                                    <td><input type="number" class="menu" name="yemek1kalori"></td>
                                </tr>
                                <tr>
                                    <th>Yemek 2</th>
                                    <td><input type="text" class="menu width90" name="yemek2"></td>
                                    <th>Kalori</th>
                                    <td><input type="number" class="menu" name="yemek2kalori"></td>
                                </tr>
                                <tr>
                                    <th>Yemek 3</th>
                                    <td><input type="text" class="menu width90" name="yemek3"></td>
                                    <th>Kalori</th>
                                    <td><input type="number" class="menu" name="yemek3kalori"></td>
                                </tr>
                                <tr>
                                    <th>İçecek</th>
                                    <td><input type="text" class="menu width90" name="icecek"></td>
                                    <th>Kalori</th>
                                    <td><input type="number" class="menu" name="icecekkalori"></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input class="menu width90" type="submit" name="diyetkaydet" value="Kaydet"></td>
                                </tr>
                            </form>
                        </table>
                        <br>
                        <p class="menu width80">Hastanın Yediği Öğünler</p>
                        <table class="tablo3">
                            <tr>
                                <th>Öğün</th>
                                <th>Yemek 1</th>
                                <th>Yemek 2</th>
                                <th>Yemek 3</th>
                                <th>İçecek</th>
                                <th>Tarih</th>
                                <th>Toplam Kalori</th>
                            </tr>
                            <?php
                            $sql = "SELECT * FROM `gonderilenyemekler` where hastaid=$id";
                            $result = mysqli_query($con, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                //Hastanın diyetisyenine gönderdiği yediği yemekler listesi
                                echo '<tr>';
                                echo '<td>' . $row['ogun'] . '</td>';
                                echo '<td>' . $row['yemek1ad'] . '</td>';
                                echo '<td>' . $row['yemek2ad'] . '</td>';
                                echo '<td>' . $row['yemek3ad'] . '</td>';
                                echo '<td>' . $row['icecekad'] . '</td>';
                                echo '<td>' . $row['tarih'] . '</td>';

                                $yemek1 = $row['yemek1ad'];
                                $yemek2 = $row['yemek2ad'];
                                $yemek3 = $row['yemek3ad'];
                                $icecek = $row['icecekad'];
                                $toplamkalori = 0;
                                //Bu veriler tabloda listelenmeden önce toplam kalorileri hesaplanıyor
                                $sql = "select kalori from yemekler where yemekadi='$yemek1'";
                                $result2 = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result2)) {
                                    $toplamkalori += $row['kalori'];
                                }
                                $sql = "select kalori from yemekler where yemekadi='$yemek2'";
                                $result2 = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result2)) {
                                    $toplamkalori += $row['kalori'];
                                }
                                $sql = "select kalori from yemekler where yemekadi='$yemek3'";
                                $result2 = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result2)) {
                                    $toplamkalori += $row['kalori'];
                                }
                                $sql = "select kalori from icecekler where icecekadi='$icecek'";
                                $result2 = mysqli_query($con, $sql);
                                if ($row = mysqli_fetch_assoc($result2)) {
                                    $toplamkalori += $row['kalori'];
                                }
                                //Ve tabloda bu kalori değeri de listeleniyor
                                echo '<td>' . $toplamkalori . ' kcal</td>';
                                echo '</tr>';
                            }
                            ?>
                        </table>
                    </center>
        </center>
    <?php
                }
            }
            if (isset($_GET['hastaekle'])) {
                //Eğer hasta ekle sayfasına geçildi ise;
                //Hasta ekleme formu beliriyor
    ?>
    <center>
        <p class="menu width80">Yeni Hasta Kaydı</p>
        <table class="tablo">
            <form action="index.php?hastaekle" method="POST">
                <tr>
                    <th>Kullanıcı Adı</th>
                    <td><input type="text" name="kadi"></td>
                </tr>
                <tr>
                    <th>Şifre</th>
                    <td><input type="text" name="sifre"></td>
                </tr>
                <tr>
                    <th>Ad</th>
                    <td><input type="text" name="ad"></td>
                </tr>
                <tr>
                    <th>Soyad</th>
                    <td><input type="text" name="soyad"></td>
                </tr>
                <tr>
                    <th>Doğum Tarihi</th>
                    <td><input type="date" name="dtarihi"></td>
                </tr>
                <tr>
                    <th>Cinsiyet</th>
                    <td><select name="cinsiyet">
                            <option value="Erkek">Erkek</option>
                            <option value="Kadın">Kadın</option>
                        </select></td>
                </tr>
                <tr>
                    <th>Meslek</th>
                    <td><input type="text" name="meslek"></td>
                </tr>
                <tr>
                    <th>Telefon</th>
                    <td><input type="text" name="tel"></td>
                </tr>
                <tr>
                    <th>E-Posta</th>
                    <td><input type="text" name="eposta"></td>
                </tr>
                <tr>
                    <th>Boy</th>
                    <td><input type="number" name="boy"></td>
                </tr>

                <tr>
                    <th>Kilo</th>
                    <td><input type="number" name="kilo"></td>
                </tr>

                <tr>
                    <th>Hedef Kilo</th>
                    <td><input type="number" name="hedefkilo"></td>
                </tr>

                <tr>
                    <th>Amacı</th>
                    <td><select name="istek">
                            <option value="Kilo Vermek">Kilo Vermek</option>
                            <option value="Kilo Almak">Kilo Almak</option>
                        </select></td>
                </tr>
                <tr>
                    <th></th>
                    <td><input class="menu" type="submit" name="yenikayit" value="Kayıt Oluştur"></td>
                </tr>
            </form>
        </table>
    </center>
<?php

            }
        } else {
            //GİRİŞ YAPILMAZ İSE ANA SAYFA - DİLERSENİZ DOLDURULABİLİR
        }
?>

</body>

</html>