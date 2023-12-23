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
    dlgCtrl.showCustomDlg(
      'dlg-select-hall',
      {
        oCss: {href: 'css/DlgSelectHall.css'},
        oHtml: {url: 'html/DlgSelectHall.html'},
        oJs: {src: 'js/DlgSelectHall.js'}
      },
      'Выбор зала',
      null,
      (dlg,opts) => new DlgSelectHall(dlg,opts)
    );
  } //btnSelectHallClick

  btnTrashcanClick(ev) {
    dlgCtrl.showDlg(
      'warn',
      `<p style='font-size: 18px'><b>Todo: </b>Будем удалять в специальную галерею "Отстойник" (Sump).</p>`+
      `<p style='font-style: italic'>Не забыть её добавить в Galleries.conf.php.</p>`,
      'К сведению...'
    );
  }

  btnHelpClick(ev) {
    dlgCtrl.showCustomDlg(
      'dlg-about',
      {
        oCss: {href: 'css/DlgAbout.css'},
        oHtml: {url: 'html/DlgAbout.html'},
        oJs: {src: 'js/DlgAbout.js'}
      },
      'О проекте...',
      null,
      (dlg,opts) => new DlgAbout(dlg,opts)
    );
  }

  btnRefreshClick(ev) {
    alert('Пока не реализовано');
    //refreshGallery();
  }

  btnSettingsClick(ev) {
    dlgCtrl.showCustomDlg(
      'dlg-settings',
      {
        oHtml: {url: 'html/DlgSettings.html'},
        oJs: {src: 'js/DlgSettings.js'}
      },
      'Настройки',
      null,
      (dlg,opts) => new DlgSettings(dlg,opts)
    );
  } // btnSettingsClick
}

/* --- Инициализация тулбара --- */
function initToolbar(containerId) {
  return new MainToolbar(containerId);
}
