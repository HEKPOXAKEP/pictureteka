/*
  ---------------------------
  Глобальные данные проекта
  ---------------------------
*/
const
  // режим отладки
  _dbg_=true,
  _log_=console.log,
  _warn_=console.warn,
  _err_=console.error;

// Глобальный объект данных
class MainData {
  constructor() {
    var self=this;
    // грузим информацию о продукте из VersionInfo.json
    $.getJSON('VersionInfo.json',
      function(data,textStatus,xhr) {
        self.VersionInfo=data;
        // запрос выполняется асинхронно, блять!
        document.title=self.VersionInfo.PID;
      }
    ).fail(
      function(xhr,textStatus,error) {
        emitError(`Ошибка загрузки VersionInfo.json: ${textStatus}, ${error}`);
      }
    );
  }
}

var
  _md_=new MainData();
