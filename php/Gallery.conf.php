<?php
/*
  ======================================
  Конфигурация залов галереи Пикчетеки
  --------------------------------------
  Из этого формируется реальный список галерей и залов
  ======================================
  id        - инетификатор
  name      - отбражаемое имя
  descr     - описание
  path      - полный путь к каталогу
  visible   - отображать в списке Залов
  recursive - включать подкаталоги
*/
$this->GALLERY = array(
  0 => array(
    // Обязательный элемент!
    'id' => 'Sump',
    'name' => 'Отстойник',
    'descr' => 'Временное хранилище удалённых картинок',
    'path' => 'C:\\www\\Apache24\\htdocs\\pictureteka\\data\\sump\\',
    'recursive' => false,
    'visible' => false,
  ),
  // нумерация начинается с 1
  1 => array(
    'id' => 'MainHall',
    'name' => 'Главный зал',
    'descr' => 'Главный зал Музея',
    'path' => 'E:/TheMuseum/',
    'visible' => true,
    'recursive' => false,
  ),
  array(
    'id' => 'Landscapes',
    'name' => 'Пейзажи',
    'descr' => 'Пейзажи и натюрморты',
    'path' => 'E:/TheMuseum/landscapes/',
    'visible' => true,
    'recursive' => false,
  ),
  array(
    'id' => 'Portraits',
    'name' => 'Портреты',
    'descr' => 'Зал портретов',
    'path' => 'E:/TheMuseum/portraits/',
    'visible' => true,
    'recursive' => false,
  ),
  array(
    'id' => 'Storeroom',
    'name' => 'Запасник',
    'descr' => 'Не включено в экспозицию',
    'path' => 'E:/TheMuseum/storeroom/',
    'visible' => false,
    'recursive' => true,
  ),
);
?>
