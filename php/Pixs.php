<?php
/*
  ============================
  Классы работы с картинками
  ============================
*/

//namespace pictures;

class BasePicture {

  protected string $fName;

  public function __construct(string $fName) {
    $this->setFName($fName);
  }

  public function getFName() {
    return $this->fName;
  }

  public function setFName(string $fName) {
    $this->fName=$fName;
  }
}

class JpegPicture extends BasePicture {
}

class GifPicture extends BasePicture {
}

class BmpPicture extends BasePicture {
}

class PngPicture extends BasePicture {
}

class WebpPicture extends BasePicture {
}
?>
