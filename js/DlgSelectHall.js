/*
  Класс диалога выбора зала
*/
class DlgSelectHall
{
  constructor(dlg,opts) {
    this.dlg=dlg;
    this.prepareDlg();
    this.setupEvents(opts);
  }

  setupEvents(opts) {
    this.boundBtnOkClick=this.btnOkClick.bind(this);
    this.boundBtnCancelClick=this.btnCancelClick.bind(this);

    opts.buttons=[
      {text: 'Ok', id: 'btn-ok', click: this.boundBtnOkClick},
      {text: 'Cancel', id: 'btn-cancel', click: this.boundBtnCancelClick}
    ];
  }

  async prepareDlg() {
    var
      ok,
      halls;

    const
      resp=await fetch(
        'php/GetHallsList.php?op=get',{
          method: 'GET',
          credentials: 'include',
          headers: {
            'Accept': 'application/json'
          },
        });
    ///_log(resp);  //dbg

    if (!resp.ok) {
      dlgCtrl.showDlg(
        'error',
        `<p><b>Загрузка списка залов провалилась.</b></p><br>`+
        `<p><b>URL: </b>${resp.url}</p><p><b>Статус: </b>${resp.status}&nbsp;${resp.statusText}</p>`,
        'Ошибка!');
    } else {
      const
        r=resp.clone();
      try {
        halls = await resp.json();
        if (halls['err']) {
          dlgCtrl.showDlg('error',halls['msg'],'Ошибка');
        } else {
          ok=true;
        }
        ///_info('got: ', halls); //dbg
      } catch (e) {
        dlgCtrl.showDlg('error',await r.text(),'Ошибка!');
      }
    }

    if (!ok) return false;

    this.fillDlgData(halls);
  }

  fillDlgData(halls) {

  }

  btnOkClick(ev) {
    dlgCtrl.destroyDlg(this.dlg);
  }

  btnCancelClick(ev) {
    dlgCtrl.destroyDlg(this.dlg);
  }
}
