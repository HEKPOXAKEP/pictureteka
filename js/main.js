/*
  Небольшой, но очень важный
*/

function bootstrap() {
  checkDependencies();
  app=new App();
}

function checkDependencies() {
  var
    dep=[];

  if (typeof $ =='undefined') dep.push('jQuery');
  if (typeof ModCtrl =='undefined') dep.push('mod-control');
  if (typeof JqUIDlgCtrl =='undefined') dep.push('JqUIDlgCtrl');
  if (typeof Toolbar =='undefined') dep.push('Toolbar');
  if (typeof ToolbarCtrl =='undefined') dep.push('ToolbarCtrl');

  if (dep.length >0)
    alert('Не загружены основные модули:\n\n'+dep.toString()+'\n\nНормальная работа программы под вопросом.');
}
