<?php
/*
  ================================================================
  Сохраняет информационный файл подзала в $path/pictureteka.info
  ================================================================
  Возвращает ответ в формате строки json: [err, msg]
*/
require_once('msgs_ru.php');
require_once('Config.php');
require_once('GalleryMill.php');

$mill=new GalleryMill(null);

exit($mill->storeSubhallInfo())
?>
