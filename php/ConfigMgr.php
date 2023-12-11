<?php
/*
  Выдаёт клиенту массив конфигурации в формате json
*/
try {
  require_once('msgs_ru.php');
  require_once('Config.php');

  $cfg = new Config();

  if (!isset($_GET['op'])) {
    exit(json_encode(['err'=>-1971,'msg'=>'ConfigMgr.php: '.MSGS['E_NoOp']]));
  }

  $op=$_GET['op'];

  switch ($op) {
    case 'get':
      exit($cfg->getConfData(true));
      break;
    default:
      exit(json_encode(['err' => -1971, 'msg' => MSGS['E_UnrecognizedCmd'].' '.$op]));
  }
} catch (Throwable $e) {
  //error_log('CATCH >>'.$e->getMessage().'<<');
  //header('Content-Type: application/json');
  //echo json_encode(['err'=>-1971,'msg'=>'Поймали исключение: '.$e->getMessage()]);
  exit('<br><b>Обратитесь к разработчику.</b><br>'); //json_encode(['err'=>-1971,'msg'=>'Поймали исключение: '.$e->getMessage()]));
}
?>
