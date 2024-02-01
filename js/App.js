/*
  Класс App(lication)
*/

class App {
  // global application props
  versionInfo={};

  // the Constructor
  constructor() {
    this.initGlobals();
    this.loadVersionInfo();
    this.initToolbar();

    this.showHallThumbs();
  }

  /*
    Инициализация глобальных данных
  */
  initGlobals() {
    this.hallIdx=getCookie('hi');
    if (!this.hallIdx) {
      this.setHallIdx(1);
    }
  }

  /*
    Устанавливаем индекс текущего зала
    и сбрасываем указатель на текущую страницу
  */
  setHallIdx(idx) {
    this.hallIdx=idx;
    setCookie('hi',1);  // $cfg->hallIdx
    setCookie('pg',1);  // %cfg->pgNum
  }

  /*
    Грузим информацию о продукте из VersionInfo.json
  */
  loadVersionInfo() {
    fetch('VersionInfo.json')
      .then((response) => {
        if (response.ok)
          return response.json();

        return Promise.reject(`Ошибка загрузки VersionInfo.json: ${response.status} ${response.statusText}`);
      })
      .then((json) => {
        this.versionInfo=json;
        document.title=json.PID;
      })
      .catch((error) => {
        console.error(error);
    });
  }

  initToolbar() {
    toolbarCtrl.loadToolbar(
      'mainToolbar',
      'html/MainToolbar.html','js/MainToolbar.js',
      'mainToolbar');
  }

  /*
    Отображает миниатюры текущего зала.
  */
  showHallThumbs() {
    var ht=$.getJSON('php/GetHallThumbs.php',(json) => {
      $('#mainContent').html(json.thumbs);
    })
    //$('#mainContent').html();
  }
}
