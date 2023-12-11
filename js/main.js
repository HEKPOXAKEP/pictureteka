/*
  Небольшой, но очень важный
*/

function bootstrap() {
  app=new App();
}

function emptyDlg(dlgId) {
  $(dlgId).empty();
}

function closeDlg(dlgId,emptyIt=true) {
  $(dlgId).dialog('destroy');
  if (emptyIt) emptyDlg(dlgId);
}

/*
  Подготовка информационного диалога.
  dlgType: error, warn, info
*/
function setupCustomDlg(dlgType) {
  const
    dlg=document.getElementById('msg-dlg');

  dlg.className=dlgType;
  dlg.innerHTML='';

  return dlg;
}

function showCustomDlg(dlgType,msg,title) {
  setupCustomDlg(dlgType).innerHTML=msg;

  $('#msg-dlg').dialog({
    'title': title ?? dlgType,
    resizable: false,
    modal: true,
    width: 'auto',
    buttons: [
      {text: 'Ok', click: () => closeDlg('#msg-dlg')},
    ]
  });
}
