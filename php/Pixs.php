<?php
/*
  ============================
  Классы работы с картинками
  ============================
*/

namespace pictures;

class BasePicture {
  public function __construct(private string $fName) {
  }

  public function getFName() {
    return $this->fName;
  }

  public function setFName(string $fName) {
    $this->fName = $fName;
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
