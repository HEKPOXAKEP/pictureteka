<?php
/*
  Генерация миниатюре текущего зала
*/
require_once('msgs_ru.php');
require_once('GalleryMill.php');

$mill=new GalleryMill(null);

exit(json_encode($mill->createHallThumbs()));
?>

