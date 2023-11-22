<?php
/*
  =============================================
  Генерация списка галерей из заданного файла
  =============================================
*/
require_once('languages.php');
require_once('Config.php');

$cfg = new Config();

//var_dump($cfg); exit;

if (!isset($cfg->galFName)) {
  $s = MSGS['E_NoGalleryList'];
  error_log($s,0);
  exit(json_encode(array('err'=>-1971,'msg'=>$s)));
}
?>
