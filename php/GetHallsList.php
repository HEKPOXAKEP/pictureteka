<?php
/*
  ===================================================================
  Генерация списка галерей из конфигурационного файла cfg->galFName
  ===================================================================
*/
require_once('msgs_ru.php');
require_once('Config.php');
require_once('GalleriesMill.php');

$mill=new GalleriesMill(null);

exit($mill->GrindGallery(true));
?>
