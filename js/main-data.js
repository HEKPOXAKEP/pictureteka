/*
  ---------------------------
  Глобальные данные проекта
  ---------------------------
*/
const
  // режим отладки
  _log=console.log,
  _warn=console.warn,
  _info=console.info;
  _err=console.error;

const
  _ERROR='Ошибка';

var
  app=null,
  modCtrl=new ModCtrl(),
  dlgCtrl=new JqUIDlgCtrl('dialogs',modCtrl),
  toolbarCtrl=new ToolbarCtrl();
