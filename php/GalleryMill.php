<?php
/*
  ====================================================================
  Мельница для перемалываения Gallery.conf.php в Galleries.conf.json
  ====================================================================
  Так же записывает pictureteka.info и генерирует миниатюры залов
*/
require_once('msgs_ru.php');
require_once('Config.php');
///require_once('Dbg.php');  //dbg

const
  thumbSize=64,
  PICTURETEKA_INFO='pictureteka.info';

class GalleryMill
{
  public ?Config $cfg=null;
  public array $GALLERY;
  public array $rez=[];  // результирующий массив: сообщение об ошибке или строка json

  /*
    Дабавляет конечный слэш к пути, если его там нет
  */
  static function addTrailingSlash(string $s): string {
    return (mb_substr($s,-1)=="\\" || mb_substr($s,-1)=='/') ? $s : $s.DIRECTORY_SEPARATOR;
  }
  /*
    Удаляет уонечные слэши из пути
  */
  static function delTrailingSlash(string $s): string {
    return rtrim($s,'/\\');
  }

  /*
    Удаляем из массива элементы с пустым значением
  */
  static function removeEmptyVals(array &$a) {
    foreach($a as $k=>$v) {
      if (gettype($v) ==='string') {
        $v = trim($v);
        $a[$k] = $v;
        if ($v === '') unset($a[$k]);
      }
    }
  }

  /*
    Приводит строку к будеву типу
  */
  static function toBool(string $v): bool {
    $v=strtolower(substr($v,0,5));

    if ($v ==='true')
      return true;
    else
      return false;
  }

  /* -------------- class begins here ------------- */

  public function __construct(?Config $cfg) {
    if (!$cfg)
      $this->cfg=new Config();
    else
      $this->cfg=$cfg;
  }

  /*
    Перемалываем Gallery.conf.php -> Gallery.conf.json
  */
  public function grindGallery(bool $storeJsonFile): string {
    if (!isset($this->cfg->galFName)) {
      $s=MSGS['E_NoGalleryList'];
      return(json_encode(array('err'=>-1971,'msg'=>$s)));
    }

    include_once($this->cfg->galFName);

    for ($i=0; $i <=array_key_last($this->GALLERY); $i++) {
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
    $gal['path']=GalleryMill::addTrailingSlash(
      mb_eregi_replace('[\\\/]',DIRECTORY_SEPARATOR,$gal['path'])
    );

    // сначала добавляем сам зал
    $gal['subhall']=false;
    /*if (count($this->rez) ==0)
      $this->rez[1]=$gal;
    else
      $this->rez[]=$gal;*/
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
        $path=GalleryMill::addTrailingSlash($path);

        $name='';
        $descr='';
        $thumb='';

        // если есть в каталоге подзала файл pictureteka.info,
        if (file_exists($path.PICTURETEKA_INFO)) {
          // там могут быть ключи ignore, descr, name, thumb
          $pi=file_get_contents($path.PICTURETEKA_INFO);
          $pi=json_decode($pi,JSON_OBJECT_AS_ARRAY);

          if (isset($pi['ignore']) && $pi['ignore']) continue;

          $name=isset($pi['name']) ? $pi['name'] : '';
          $descr=isset($pi['descr']) ? $pi['descr'] : '';
          $thumb=isset($pi['thumb']) ? $pi['thumb'] : '';
        }

        $this->rez[]=[
          'id'=>'subhall-'.end($ap),
          'name'=>($name=='') ? GalleryMill::delTrailingSlash(mb_substr($path,mb_strlen($gal['path']))) : $name,
          'descr'=>$descr,
          'path'=>$path,
          'thumb'=>$thumb,
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

  /*
    Проверяет параметры $_POST для сохранения pictureteka.info.
    Парамнтры:
      path    - ОБЯЗАТЕЛЬНЫЙ путь к залу
      name    - название
      descr   - описание
      ignore  - true||false
      thumb   - имя миниатюры
  */
  private function checkStoreInfoParams(): array {
    $rez=['err'=>0,'msg'=>'ok','params'=>[]];

    if (!isset($_POST['path'])) {
      return(['err'=>-1971,'msg'=>MSGS['E_ParamNotSpecified']]);
    }

    $rez['path']=GalleryMill::addTrailingSlash($_POST['path']);

    $rez['params']['name']=(isset($_POST['name'])) ? $_POST['name'] : '';
    $rez['params']['descr']=(isset($_POST['descr'])) ? $_POST['descr'] : '';
    $rez['params']['ignore']=false;  // !!!
    if (isset($_POST['ignore']))
      $rez['params']['ignore']=GalleryMill::toBool($_POST['ignore']);
    $rez['params']['thumb']=(isset($_POST['thumb'])) ? $_POST['thumb'] : '';

    GalleryMill::removeEmptyVals($rez['params']);

    return($rez);
  }

  /*
    Сохраняет информационный файл подзала в pictureteka.info
  */
  public function storeSubhallInfo(): string {
    $rez=$this->checkStoreInfoParams();
    if ($rez['err'] !==0) {
      exit(json_encode(['err'=>$rez['err'],'msg'=>$rez['msg']]));
    }

    file_put_contents(
      $rez['path'].PICTURETEKA_INFO,
      json_encode(
        $rez['params'],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
      )
    );

    return(json_encode(['err'=>0,'msg'=>'ok']));
  }

  /*
    Генерирует миниатюру зала
  */
  public function getHallThumb() {
    $fn=$_GET['th'];
    if ($fn==='' || !file_exists($fn)) {
      $this->emitNotFound();
      exit;
    }
    $dim=getimagesize($fn);
    $ww=$dim[0]; $hh=$dim[1];

    $r1=$ww/thumbSize; $r2=$hh/thumbSize;
    $ratio=$r1 >$r2 ? $r1:$r2;
    if ($ratio <1) $ratio=1;
    $w=intval($ww/$ratio); $h=intval($hh/$ratio);

    switch($dim[2]) {
      case 1: $img=imagecreatefromgif($fn); break;
      case 2: $img=imagecreatefromjpeg($fn); break;
      case 3: $img=imagecreatefrompng($fn); break;
      default: emitNotFound(); exit;
    }

    if (!$img) {
      $this->emitNotFound();
      exit;
    }

    imagealphablending($img,true);
    $thumb=imagecreatetruecolor($w,$h);

    imagecopyresampled($thumb,$img,0,0,0,0,$w,$h,$ww,$hh);

    /*if ($dim[2] ==2)
      $thumb=rotateImage($fn,$thumb,$w,$h);*/

    header('Content-Type: image/jpeg');
    imagejpeg($thumb,NULL,80);

    imagedestroy($thumb);
    imagedestroy($img);
  }

  /*
    Выдаст картинку [:-(]
  */
  public function emitNotFound() {
    header('Content-type: image/png',true,404);
    header('Content-Disposition: attachment; filename="notfound.png"');
    readfile('../img/notFound.png');
  }
} // --- End of Class GalleriesMill
?>
