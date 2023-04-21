/*
  Небольшой, но очень важный
*/

/*
  Вешаем обработчк глобальных ошибок
*/
window.addEventListener('unhandledrejection',function(ev) {
  emitError(ev.promise);
  emitError(ev.reason);
});

function bootstrap() {
  initMainToolbar();
}

function initMainToolbar() {
  $.getScript('js/main-toolbar.js')
    .done(function(script,textStatus){
      initToolbar('mainToolbar','html/main-toolbar.html');
    })
    .fail(function(xhr,settings,exception){
      _log_('Ошибка initMainToolbar.getScript:',settings,exception);
    });
}

function refreshGallery() {
  _log_('--- обновление галереи...');
}
