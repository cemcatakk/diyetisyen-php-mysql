<?php
session_start();

session_destroy();
header('Location:index.php');
//Çıkış düğmesine basıldığında session bilgileri boşaltılıyor.
