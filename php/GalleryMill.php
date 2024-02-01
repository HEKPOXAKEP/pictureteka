<?php
/*
  ========================================================
  Мельница галереи. Главный объект.
  ========================================================
  - Перемалывает Gallery.conf.php в Galleries.conf.json
  - Записывает pictureteka.info для подзалов
  - Генерирует миниатюры залов
*/
require_once('msgs_ru.php');
require_once('Functions.php');
require_once('Config.php');
require_once('PixUtils.php');
///require_once('Dbg.php');  //dbg

const
  thumbSize=64,                         // размер миниатюры зала по умолчанию
  PICTURETEKA_INFO='pictureteka.info',  // имя файла параметров подзала

  // типы возваращаемых данных
  RT_NONE=0,    // return w/o param
  RT_ARRAY=1,   // array
  RT_JSON=2,    // json string

  // индексы массива параметров getimagesize()
  I_WIDTH=0,      // ширина
  I_HEIGHT=1,     // высота
  I_TYPE=2,       // константа типа изображения IMAGETYPE_XXX
  I_SIZEATTR=3   // строка height="yyy" width="xxx"
  ;

class GalleryMill
{
  public ?Config $cfg=null;
  public array $GALLERY;
  ///public array $rez=[];  // результирующий массив: сообщение об ошибке или строка json


  /* -------------- class begins here ------------- */

  public function __construct(?Config $cfg) {
    if (!$cfg)
      $this->cfg=new Config();
    else
      $this->cfg=$cfg;
  }

  /*
    Перемалываем Gallery.conf.php -> Gallery.conf.json.
    Возвращает переработанный массив залов или сообщение об ошибке [err,msg].
  */
  public function grindGallery(bool $storeJsonFile=false): array {
    $rez=[];

    if (!isset($this->cfg->galFName)) {
      $s=MSGS['E_NoGalleryList'];
      return(array('err'=>-1971,'msg'=>$s));
    }

    include_once($this->cfg->galFName);

    for ($i=0; $i <=array_key_last($this->GALLERY); $i++) {
      if ($this->GALLERY[$i]['visible'])
        $this->doGrindHall($this->GALLERY[$i],$rez);
    }

    if ($storeJsonFile) $this->storeJsonGallery($rez);

    return($rez);
  }

  /*
    Добавляет в массив &$rez зала $gal и его под залов, если recursive==true
  */
  private function doGrindHall(array $gal, array &$rez) {
    $gal['path']=addTrailingSlash(
      mb_eregi_replace('[\\\/]',DIRECTORY_SEPARATOR,$gal['path'])
    );

    // сначала добавляем сам зал
    $gal['subhall']=false;
    $rez[]=$gal;

    // теперь подзалы, если recursive==true
    if (!$gal['recursive']) return;

    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($gal['path'], RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $path=>$obj) {
      if ($obj->isDir()) {
        $ap=explode(DIRECTORY_SEPARATOR,$path);
        $path=addTrailingSlash($path);

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

        // добавляем подзал
        $rez[]=[
          'id'=>'subhall-'.end($ap),
          'name'=>($name=='') ? delTrailingSlash(mb_substr($path,mb_strlen($gal['path']))) : $name,
          'descr'=>$descr,
          'path'=>$path,
          'thumb'=>$thumb,
          'subhall'=>true,
        ];
      }
    }
  }

  /*
    Записываем $rez в формате json в файл {$fname}.json
    и устанавливает его mtime == mtime(galFName)
  */
  private function storeJsonGallery(array &$rez) {
    $fn=$this->mkJsonFName($this->cfg->galFName);
    file_put_contents(
      $fn,
      json_encode(
        $rez,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
      ) //  | JSON_UNESCAPED_LINE_TERMINATORS
    );

    touch($fn,filemtime($this->cfg->galFName));
  }

  /*
    Вернёт имя json-файла списка залов: cfg->galFName.json
  */
  public function mkJsonFName(string $fname) {
    return(preg_replace('/\.[^.]+$/','.',$fname).'json');
  }

  /*
    Загружает из json-файла список залов и возваращает в виде массива
  */
  public function loadJsonGallery() {
    $fn=$this->mkJsonFName($this->cfg->galFName);

    if (!file_exists($fn) || filemtime($fn) !==filemtime($this->cfg->galFName)) {
      $rez=$this->grindGallery(true);
      $msg='ok. перезаписан.';
    } else {
      $rez=json_decode(file_get_contents($fn),JSON_OBJECT_AS_ARRAY);
      $msg='ok. загружен.';
    }

    return(array('err'=>0,'msg'=>$msg,'halls'=>$rez));
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

    $rez['params']['ignore']=false;  // fckng sht! ? operator doesnt work with bools!
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

    if (file_exists($this->mkJsonFName($this->cfg->galFName)))
      touch($this->mkJsonFName($this->cfg->galFName));

    return(json_encode(['err'=>0,'msg'=>'ok']));
  }

  /*
    Генерирует миниатюру зала.

    В _GET передаются:
      hi - индекс зала в массиве залов
      th - имя файла-миниатюры
  */
  public function getHallThumb() {
    $g=$this->loadJsonGallery();
    if ($g['err']) {
      $this->emitNotFound();
      exit;
    }

    $idx=$_GET['hi'];
    $fn=$g['halls'][$idx]['path'].$_GET['th'];

    if ($fn==='' || !file_exists($fn)) {
      $this->emitNotFound();
      exit;
    }

    $dim=getimagesize($fn);
    $ww=$dim[I_WIDTH]; $hh=$dim[I_HEIGHT];

    // вычисляем размеры будущей миниатюры
    $r1=$ww/thumbSize; $r2=$hh/thumbSize;
    $ratio=$r1 >$r2 ? $r1:$r2;
    if ($ratio <1) $ratio=1;
    $w=intval($ww/$ratio); $h=intval($hh/$ratio);

    switch($dim[I_TYPE]) {
      case IMAGETYPE_GIF: $img=imagecreatefromgif($fn); break;
      case IMAGETYPE_JPEG: $img=imagecreatefromjpeg($fn); break;
      case IMAGETYPE_PNG: $img=imagecreatefrompng($fn); break;
      case IMAGETYPE_WEBP: $img=imagecreatefromwebp($fn); break;
      default: $this->emitNotFound(); exit;
    }

    if (!$img) {
      $this->emitNotFound();
      exit;
    }

    imagealphablending($img,true);
    $thumb=imagecreatetruecolor($w,$h);

    imagecopyresampled($thumb,$img,0,0,0,0,$w,$h,$ww,$hh);

    if ($dim[I_TYPE] ==IMAGETYPE_JPEG)
      $thumb=PixUtils::rotateJpegImg($fn,$thumb,$w,$h);

    header('Content-Type: image/png');
    imagepng($thumb);

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

  /*
    Вернёт страницу миниатюр для текущего зала.
    Из cfg используются параметры:
      hallIdx
      pgNum
      thSize
      perPage
  */
  public function createHallThumbs() {
    $g=$this->loadJsonGallery();
    // проверяем ошибку при загрузке залов
    if ($g['err']) return($g);

    $dir=$g['halls'][$this->cfg->hallIdx]['path'];
    $rez='';

    if ($h=opendir($dir)) {
      while (($f=readdir($h)) !==false) {
        if (is_file($dir.$f)) {
          $ext=strtolower(strrchr($f,'.'));
          if (in_array($ext,PIX_EXT)) {
            $rez.='<div class="img-thumb">'.$f.'</div>';
          }
        }
      }
      closedir($h);
      return(['err'=>0,'msg'=>'ok','thumbs'=>$rez]);
    } else {
      return(['err'=>-1971,'msg'=>MSGS['E_UnableToOpenDir'].$dir]);
    }
  }
} // --- End of Class GalleryMill ---
?>
