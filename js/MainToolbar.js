/*
  ------------------------
  Класс главного тулбара
  ------------------------
*/
class MainToolbar extends Toolbar {

  constructor(containerId) {
    super(containerId);
  }

  /* -------------- tool buttons click handlers --------------- */

  toolbarBtnClick(ev) {
    var btnId=super.toolbarBtnClick(ev);

    ///console.log('btnId = ',btnId);  //dbg

    switch(btnId) {
      case 'select_hall':
        this.btnSelectHallClick(ev);
        break;
      case 'refresh':
        this.btnRefreshClick(ev);
        break;
      case 'trashcan':
        this.btnTrashcanClick(ev);
        break;
      case 'settings':
        this.btnSettingsClick(ev);
        break;
      case 'help':
        this.btnHelpClick(ev);
        break;
      default:
        _warn('Непонятная кнопка: ',btnId);
    }
  }

  btnSelectHallClick(ev) {
    $('#dlg').load(
      'html/DlgSelectHall.html',
      (respText,txtStatus,xhr) => {
        if (txtStatus =='error')
          showCustomDlg(
            'error',
            `Ошибка загрузки html/DlgSelectHall.html:<br>${xhr.status} : ${xhr.statusText}`,
            'Ошибка!');
        else
          this.execSelectHallDlg();
      }
    ); //.load
  } //btnSelectHallClick

  btnTrashcanClick(ev) {
    showCustomDlg(
      'warn',
      `<p style='font-size: 18px'><b>Todo: </b>Будем удалять в специальную галерею "Отстойник" (Sump).</p>`+
      `<p style='font-style: italic'>Не забыть её добавить в Galleries.conf.php.</p>`,
      'К сведению...'
    );
  }

  btnHelpClick(ev) {
    showCustomDlg(
      'info',
      `<p style='font-size: 18px'><b>${app.versionInfo.PID}</b></p>`+
      `${app.versionInfo.DESCR}<br><br>Версия ${app.versionInfo.VID}<br><br>${app.versionInfo.CID}`,
      'Эбаут');
  }

  btnRefreshClick(ev) {
    alert('Пока не реализовано');
    //refreshGallery();
  }

  btnSettingsClick(ev) {
    emptyDlg('#dlg');

    $('#dlg').load(
      'html/DlgConfig.html',
      (respText,txtStatus,xhr) => {
        if (txtStatus =='error')
          showCustomDlg(
            'error',
            `Ошибка загрузки html/DlgConfig.html:<br>${xhr.status} : ${xhr.statusText}`,
            'Ошибка!');
        else
          this.execSettingsDlg();
      }
    ); //.load
  } // btnSettingsClick

  /*
    Отображение диалога настроек
  */
  async execSettingsDlg() {
    var
      ok=false,
      cfgData;

    const
      resp=await fetch(
      'php/ConfigMgr.php?op=get',{
        method: 'GET',
        credentials: 'include',
        headers: {
          'Accept': 'application/json'
        },
      });
    ///_log(resp);  //dbg
    if (!resp.ok) {
      showCustomDlg(
        'error',
        `<p><b>Загрузка параметров конфигурации обломалась.</b></p><br><p><b>URL: </b>${resp.url}</p><p><b>Статус: </b>${resp.status}&nbsp;${resp.statusText}</p>`,
        'Ошибка!');
    } else {
      const
        r=resp.clone();
      try {
        cfgData = await resp.json();
        if (cfgData['err']) {
          showCustomDlg('error',cfgData['msg'],'Ошибка');
        } else {
          ok=true;
        }
        ///_info('got: ', cfgData); //dbg
      } catch (e) {
        showCustomDlg('error',await r.text(),'Ошибка!');
      }
    }

    if (!ok) return false;

    document.getElementById('edit-galfname').value=cfgData['edit-galfname'];
    document.getElementById('edit-thsize').value=cfgData['edit-thsize'];
    document.getElementById('edit-perpage').value=cfgData['edit-perpage'];

    $('#dlg').dialog({
      title: 'Настройки',
      resizable: false,
      modal: true,
      width: 'auto',
      buttons: [
        {text: 'Ok', click: () =>
          this.dlgSettingsOk()
        },
        {text: 'Отмена', click: () =>
          closeDlg('#dlg')
        }
      ]
    });
  }

  /*
    По кнопке Ok диалога настроек
  */
  dlgSettingsOk() {
    setCookie('gf',document.getElementById('edit-galfname').value);
    setCookie('sz',document.getElementById('edit-thsize').value);
    setCookie('pp',document.getElementById('edit-perpage').value);

    closeDlg('#dlg');
  }

  /*
    Отображение диалога выбора зала
  */
  async execSelectHallDlg() {
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
      showCustomDlg(
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
          showCustomDlg('error',halls['msg'],'Ошибка');
        } else {
          ok=true;
        }
        ///_info('got: ', halls); //dbg
      } catch (e) {
        showCustomDlg('error',await r.text(),'Ошибка!');
      }
    }

    if (!ok) return false;

    $('#dlg').dialog({
      title: 'Выберите зал',
      resizable: false,
      modal: true,
      width: 'auto',
      buttons: [
        {text: 'Ok', click: () =>
            this.dlgSelectHallOk()
        },
        {text: 'Отмена', click: () =>
            closeDlg('#dlg')
        }
      ]
    });
  }

  dlgSelectHallOk() {
    closeDlg('#dlg');
  }
}

/* --- Инициализация тулбара --- */
function initToolbar(containerId) {
  return new MainToolbar(containerId);
}
