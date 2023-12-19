<?php
/*
  ==============================
  Класс работы с конфигурацией
  ==============================
*/
require_once('TraitConfData.php');

class Config {
  use confData; // include trait confData

  public function __construct() {
    // заполняем данные из _COOKIE
    $this->galIdx = $_COOKIE['gi'] ?? 1;
    $this->galFName = $_COOKIE['gf'] ?? 'Gallery.conf.php';  // then remove (?)
    $this->hallIdx = $_COOKIE['hi'] ?? 1;
    $this->pgNum = $_COOKIE['pg'] ?? 1;
    $this->thSize = $_COOKIE['sz'] ?? 80;
    $this->perPage = $_COOKIE['pp'] ?? 30;
  }

  /*
    Вернёт данные в формате json для заполнения формы DlgConfig
  */
  public function getConfData($asJson=false) {
    $a=[
      'err'=>0, 'msg'=>'Ok',
      'gallery-idx'=>$this->galIdx,
      'select-gallery'=>$this->galleries,
      'edit-galfname'=>$this->galFName,
      'edit-thsize'=>$this->thSize,
      'edit-perpage'=>$this->perPage,
    ];
    if ($asJson)
      return json_encode($a);
    else
      return $a;
  }
}
?>
