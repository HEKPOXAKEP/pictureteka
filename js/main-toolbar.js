/*
  ------------------------
  Класс главного тулбара
  ------------------------
*/
class MainToolbar extends ToolBar {
  constructor(containerId,htmlName) {
    super(containerId,htmlName);
  }

  bindEvents() {
    $('#toolbtn-refresh').click(this,this.btnRefreshClick);
  }

  /* -------------- click handlers ------------------ */
  btnRefreshClick(ev) {
    _log_(ev.data);
    refreshGallery();
  }
}

/* --- Инициализация --- */
function initToolbar(containerId,htmlName) {
  return new MainToolbar(containerId,htmlName);
}
