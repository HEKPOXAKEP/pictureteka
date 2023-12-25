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
  }

  /*
    Инициализация глобальных данных
  */
  initGlobals() {
    this.hallIdx=getCookie('hi');
    if (!this.hallIdx) {
      this.hallIdx=1;
      setCookie('hi',1);
    }
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
}
