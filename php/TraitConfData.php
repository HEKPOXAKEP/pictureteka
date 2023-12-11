<?php
/*
  ===================================
  Трейт конфигурационных параметров
  ===================================
*/
trait  confData {
  public array $galleries=[
    'Выберите файл описания залов',
    'Gallery.conf.php',
    '__Gallery.conf.php'
  ];
  public int $galIdx;       // индекс текущего элемента в списке файлов галерей $this->galleries [gi]
  public string $galFName;  // имя файла описания залов; def: Gallery.conf.php [gf]
  public int $hallIdx;      // индекс текущего зала в файле $this->gaFName [hi]
  public int $pgNum;        // номер текущей страницы [pg]
  public int $thSize;       // размер миниатюры [sz]
  public int $perPage;      // к-во миниатюр на страницу [pp]
}
?>
