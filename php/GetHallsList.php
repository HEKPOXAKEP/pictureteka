<?php
/*
  ===================================================================
  Генерация списка галерей из конфигурационного файла cfg->galFName
  ===================================================================
  Возвращает список залов в формате строки json
*/
require_once('msgs_ru.php');
require_once('Config.php');
require_once('GalleryMill.php');

$mill=new GalleryMill(null);

exit($mill->grindGallery(true));
?>
