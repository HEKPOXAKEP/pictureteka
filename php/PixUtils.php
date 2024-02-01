<?php
/*
  Набор функций для работы с изображениями
*/
const
  // расширения файлов-картинок
  PIX_EXT=['.jpg','.jpeg','.gif','.png','.bmp','.webp'];

class PixUtils
{
  /*
    Вернёт EXIF указанного файла в виде массива, либо FALSE
  */
  public static function getEXIFasArray(string $fn) {
    if (false ===$exif=exif_read_data($fn,'ANY_TAG',true,false)) {
      return false;
    } else {
      return $exif;
    }
  }

  /*
    Вернёт EXIF указанного файла в виде строки, либо FALSE
  */
  public static function getEXIFasStr(string $fn): string {
    if (false ==$exif=PixUtils::getEXIFasArray($fn)) {
      return false;
    } else {
      ///$s='';
      ///foreach($exif as $k=>$v) {
      $s=print_r($exif,true);

      ///$s.=$k.'='.$v.'<br>';
      ///}
      return $s;
    }
  }

  /*
    Вернёт ориентацию jpeg указанного файла
  */
  public static function getJpegImgOrientation(string $fn): int {
    if (false ==$exif=PixUtils::getEXIFasArray($fn)) {
      return 1;
    } elseif (array_key_exists('Orientation',$exif)) {
      return $exif['Orientation'];
    } elseif (array_key_exists('IFD0',$exif)) {
      if (array_key_exists('Orientation',$exif['IFD0'])) {
        return $exif['IFD0']['Orientation'];
      }
      return 1;
    }
  }

  /*
    Обработка ориентации картинки по EXIF[IFD0][Orientation]
      3: поворот на 180
      6: поворот на 90 по часовой
      8: поворот на 90 против часовой
      остальные (1,2,4,5,7 и вообще другие-левые) - ничего не делаем

    Возвращает повёрнутое изображение.
    Кроме того, при повороте на (-)90 градусов width и height поменяются местами.
  */
  public static function rotateJpegImg(string $fn, GdImage &$img, int &$w,int &$h): GdImage
  {
    $o=PixUtils::getJpegImgOrientation($fn);

    switch ($o) {
      case 3:       // rotate cw||ccw 180 (вверх ногами)
        $r = 180;
        break;
      case 6:       // rotate cw 90
        $r = -90;
        break;
      case 8:       // rotate ccw 90
        $r = 90;
        break;
      default:
        $r = 0;
    }

    if (in_array($r,array(6,8))) PixUtils::swap($w,$h);

    if ($r) {
      return imagerotate($img,$r,0);
    } else {
      return $img;
    }
  }
}
?>
