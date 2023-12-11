<?php
/*
  Мельница для перемалываения Gallery.conf.php в Galleries.conf.json
*/
require_once('msgs_ru.php');
require_once('Config.php');

require_once('Dbg.php');  //dbg

class GalleriesMill
{
  public ?Config $cfg=null;
  public array $GALLERY;
  public array $rez=[];  // результирующий массив: сообщение об ошибке или строка json

  /*
    Дабавляет конечный слэш к пути, если его там нет
  */
  static function addTrailingSlash($s) {
    return (mb_substr($s,-1)=="\\" || mb_substr($s,-1)=='/') ? $s : $s.DIRECTORY_SEPARATOR;
  }

  public function __construct(?Config $cfg) {
    if (!$cfg)
      $this->cfg=new Config();
    else
      $this->cfg=$cfg;
  }

  /*
    Перемалываем Gallery.conf.php -> Gallery.conf.json
  */
  public function GrindGallery(bool $storeJsonFile): string {
    if (!isset($this->cfg->galFName)) {
      $s=MSGS['E_NoGalleryList'];
      return(json_encode(array('err'=>-1971,'msg'=>$s)));
    }

    include_once($this->cfg->galFName);

    for ($i=1; $i <=array_key_last($this->GALLERY); $i++) {
      if ($this->GALLERY[$i]['visible'])
        $this->doGrindHall($this->GALLERY[$i]);
    }

    if ($storeJsonFile) $this->storeJson($this->cfg->galFName);

    return(json_encode($this->rez));
  }

  /*
    Добавляет в массив $this->rez зала $gal и его под залов, если recursive==true
  */
  private function doGrindHall(array $gal) {
    $gal['path']=GalleriesMill::addTrailingSlash(
      mb_eregi_replace('[\\\/]',DIRECTORY_SEPARATOR,$gal['path'])
    );

    // сначала добавляем сам зал
    if (count($this->rez) ==0)
      $this->rez[1]=$gal;
    else
      $this->rez[]=$gal;

    // теперь подзалы, если recursive==true
    if (!$gal['recursive']) return;

    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($gal['path'], RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $path=>$obj) {
      if ($obj->isDir()) {
        $ap=explode(DIRECTORY_SEPARATOR,$path);
        $path=GalleriesMill::addTrailingSlash($path);

        $descr='';
        $name='';

        if (file_exists($path.'pictureteka.info')) {
          // если есть в каталоге подзала файл pictureteka.info,
          // там могут быть ключи ignore, descr, name
          $pi=file_get_contents($path.'pictureteka.info');
          $pi=json_decode($pi,JSON_OBJECT_AS_ARRAY);

          if (isset($pi['ignore']) && $pi['ignore']) continue;

          $descr=isset($pi['descr']) ? $pi['descr'] : '';
          $name=isset($pi['name']) ? $pi['name'] : '';
        }

        $this->rez[]=[
          'id'=>'subhall-'.end($ap),
          'name'=>($name=='') ? mb_substr($path,mb_strlen($gal['path'])) : $name,
          'descr'=>$descr,
          'path'=>$path,
          'subhall'=>true,
        ];
      }
    }
  }

  /*
    Записываем $this->rez в формате json в файл $fname.json
  */
  private function storeJson(string $fname) {
    //'__gallery.conf.php',
    file_put_contents(
      preg_replace('/\.[^.]+$/',
      '.',$fname)."json",
      json_encode(
        $this->rez,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
      ) //  | JSON_UNESCAPED_LINE_TERMINATORS
    );
  }
} // --- End of Class GalleriesMill
?>
