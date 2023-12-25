<?php
/*
  ==========================
  Поулчение миниатюры зала
  ==========================
*/
require_once('msgs_ru.php');
require_once('Config.php');
require_once('GalleryMill.php');
///require_once('Dbg.php');  //dbg

$mill=new GalleryMill(null);
$mill->getHallThumb();
?>

