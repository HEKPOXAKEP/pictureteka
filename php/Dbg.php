<?php
/*
  ====================
  Долбаггерный класс
  ====================
*/
class Dbg
{
  static function _log(string $s) {
    file_put_contents('__debug.log',$s.PHP_EOL,FILE_APPEND);
  }

  static function _json($a) {
    file_put_contents(
      '__debug.log',
      json_encode($a,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).PHP_EOL,FILE_APPEND);
  }
}

Dbg::_log(PHP_EOL.'--- '.date('d.m.Y H:i:s'));
?>
