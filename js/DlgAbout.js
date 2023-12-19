/*
  Класс диалога "О проекте"
*/
class DlgAbout
{
  constructor(dlg,opts) {
    this.dlg=dlg;
    this.prepareDlg();
    this.setupEvents(opts);
  }

  prepareDlg() {
    document.getElementById('about-PID').innerHTML=app.versionInfo.PID;
    document.getElementById('about-DESCR').innerHTML=app.versionInfo.DESCR;
    document.getElementById('about-VID').innerHTML=`Версия ${app.versionInfo.VID}`;
    document.getElementById('about-CID').innerHTML=app.versionInfo.CID;
  }

  setupEvents(opts) {
    this.boundBtnOkClick=this.btnOkClick.bind(this);

    opts.buttons=[
      {text: 'Отлично!', click: this.boundBtnOkClick}
    ];
  }

  btnOkClick(ev) {
    dlgCtrl.destroyDlg(this.dlg);
  }
}
