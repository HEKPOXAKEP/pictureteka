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

    console.log('btnId = ',btnId);

    switch(btnId) {
      case 'select_gallery':
        break;
      case 'refresh':
        this.btnRefreshClick(ev);
        break;
      case 'trashcan':
        break;
      case 'settings':
        break;
      case 'help':
        break;
      default:
        _log('Непонятная кнопка: ',btnId);
    }
  }

  btnRefreshClick(ev) {
    _log(ev);
    //refreshGallery();
  }
}

/* --- Инициализация тулбара --- */
function initToolbar(containerId) {
  return new MainToolbar(containerId);
}
