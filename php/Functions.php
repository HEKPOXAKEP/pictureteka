<?php
/*
  ==========
  Функциии
  ==========
*/

/*
  Дабавляет конечный слэш к пути, если его там нет
*/
function addTrailingSlash(string $s): string {
  return (mb_substr($s,-1)=="\\" || mb_substr($s,-1)=='/') ? $s : $s.DIRECTORY_SEPARATOR;
}

/*
  Удаляет завершающие слэши из пути
*/
function delTrailingSlash(string $s): string {
  return rtrim($s,'/\\');
}

/*
  Удаляем из массива элементы с пустым значением
*/
function removeEmptyVals(array &$a) {
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
function toBool(string $v): bool {
  $v=strtolower(substr($v,0,5));

  if ($v ==='true')
    return true;
  else
    return false;
}

/*
  Обмен переменных
*/
function swap(&$a,&$b) {
  $z=$a; $a=$b; $b=$z;
}

?>
