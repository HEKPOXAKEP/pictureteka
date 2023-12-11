<?php
/*
  ===================================
  Трейт конфигурационных параметров
  ===================================
*/
trait  confData {
  public string $galFName;    // имя файла описания галерей; def: Gallery.conf.php
  public int $galIdx;         // индекс текущей галереи в файле $this->gaFName
  public int $pgNum;          // номер текущей страницы
  public int $thSize;         // размер миниатюры
  public int $perPage;        // к-во миниатюр на страницу
}
?>
