/*
  Ксласс диалога настроек
*/
class DlgSettings
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
      ok=false,
      cfgData;

    const
      resp=await fetch(
        'php/ConfigMgr.php?op=get',
        {
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
        `<p><b>Загрузка параметров конфигурации обломалась.</b></p><br><p><b>URL: </b>${resp.url}</p><p><b>Статус: </b>${resp.status}&nbsp;${resp.statusText}</p>`,
        'Ошибка!');
    } else {
      const
        r=resp.clone();
      try {
        cfgData = await resp.json();
        if (cfgData['err']) {
          dlgCtrl.showDlg('error',cfgData['msg'],'Ошибка');
        } else {
          ok=true;
        }
        ///_info('got: ', cfgData); //dbg
      } catch (e) {
        dlgCtrl.showDlg('error',await r.text(),'Ошибка!');
      }
    }

    if (!ok) return false;

    this.fillDlgData(cfgData);
  }

  fillDlgData(cfgData) {
    var
      sel=document.getElementById('select-gallery'),
      op;

    cfgData['select-gallery'].forEach((val,idx) => {
      op=document.createElement('option');
      op.value=idx;
      op.textContent=val;
      if (idx ===0) op.disabled=true;
      sel.appendChild(op);
    });
    sel.value=cfgData['gallery-idx'];

    document.getElementById('edit-thsize').value=cfgData['edit-thsize'];
    document.getElementById('edit-perpage').value=cfgData['edit-perpage'];
  }

  btnOkClick(ev) {
    setCookie('gi',document.getElementById('select-gallery').value);
    setCookie('gf',document.getElementById('select-gallery').selectedOptions[0].text);
    setCookie('sz',document.getElementById('edit-thsize').value);
    setCookie('pp',document.getElementById('edit-perpage').value);

    dlgCtrl.destroyDlg(this.dlg);
  }

  btnCancelClick(ev) {
    dlgCtrl.destroyDlg(this.dlg);
  }
}
