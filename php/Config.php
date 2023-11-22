<?php
/*
  ========================
  Конфигурация программы
  ========================
*/
require_once('TraitConfData.php');

class Config {
  use confData;

  public function __construct() {
    // заполняем данные из _COOKIE
    $this->galFName = $_COOKIE['gf'] ?? 'Galleries.php';
    $this->galIdx = $_COOKIE['gi'] ?? 1;
    $this->pgNum = $_COOKIE['pg'] ?? 1;
    $this->thSize = $_COOKIE['sz'] ?? 120;
    $this->perPage = $_COOKIE['pp'] ?? 49;
  }
}
?>
