<?php
/*
  =====================
  Класс Thumbnailizer
  ---------------------
  Генерирует страницу с миниатюрами.
  =====================
*/
class Thumbnalizer {
  // данные берутся из $_COOKIE
  private string $path;    // каталог с картинками
  private int $pgnum;      // номер текущей страницы
  private int $perpg;      // к-во миниатюр на странице
  private int $thsize;     // размер миниатюры

  public function __construct() {
    $this->path = $_COOKIE['path'];
    $this->pgnum = $_COOKIE['pgnum'];
    $this->perpg = $_COOKIE['perpg'];
    $this->thsize = $_COOKIE['thsize'];
  }

  public function getThumbs(): string {
    // генерация страницы миниатюр
  }

  public function getPages(): string {
    // генерация строки кнопок страниц
  }
}
?>
