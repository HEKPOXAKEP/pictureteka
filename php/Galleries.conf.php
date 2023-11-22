<?php
/*
  ================================
  Конфигурация галерей Пикчетеки
  ------------------------------
  Из этого формируется реальный список галерей и залов
  ================================
  id        - инетификатор
  name      - отбражаемое имя
  descr     - описание
  path      - полный путь к каталогу
  visiable  - отображать в списке Залов
  recursive - включать подкаталоги
*/
const GALLERIES = array(
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
