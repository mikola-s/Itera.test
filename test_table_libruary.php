<?PHP
/** из переданного массива на HTML странице генерируется таблица размером 3х3,
  *в которой текст занимает соответствующие позиции
  *с соответствующим вырыванием и цветом фона*/
function merge_cells_in_table($start_array){

  //к-во строк в таблице
  $rows_in_tab = 3;
  //к-во колонок в таблице
  $cols_in_tab = 3;
  $cells_array = empty_cells_array($rows_in_tab, $cols_in_tab);
  $cells_array = start_arr_transfer($start_array, $cells_array, $rows_in_tab, $cols_in_tab);
  $cells_array = horizontal_merge($cells_array, $rows_in_tab, $cols_in_tab);
  $cells_array = vertical_merge($cells_array, $rows_in_tab, $cols_in_tab);
  output_table($cells_array, $rows_in_tab, $cols_in_tab);
}


/*возвращает $cells_array с размерностью [$rows_in_tab * $cols_in_tab]
с начальными данными по схеме офрмления*/
function empty_cells_array($rows_in_tab, $cols_in_tab){
  $cells_count = $rows_in_tab * $cols_in_tab;
  for ($cell=1; $cell <= $cells_count; $cell++) {
    $cells_arr[$cell] = array(
            'align' => '',
            'valign' => '',
            'bgcolor' => '',
            'colspan' => '1',
            'rowspan' => '1',
            'id' => '',
            'color' => '',
            'text' => '');
  }
  return $cells_arr;
}


/**в соответствии с набором чисел в ячейке ['cells']
  *массива $start_array передает данные в массив $cells_array*/
function start_arr_transfer($start_array, $cells_array, $rows_in_tab, $cols_in_tab){
  //вычисление количества ячеек
  $cells_count = $rows_in_tab * $cols_in_tab;

  //начальный идентификатор схемы оформления
  $id_color_scheme = 1;
  foreach ($start_array as $num_start_arr => $settings_start_arr) {
    for ($num_cell = 1; $num_cell <= $cells_count; $num_cell++) {

      //если номер ячейки есть в ['cells'] исходного массива
      if (strpos($settings_start_arr['cells'], (string) $num_cell) !== FALSE) {
        foreach ($settings_start_arr as $name_setting_start_arr => $value_setting_start_arr) {

          //передача данных в массив $cells_array кроме данных из ячейки "cells"
          if ($name_setting_start_arr != 'cells') {
            $cells_array[$num_cell][$name_setting_start_arr] = $value_setting_start_arr;
          }
        }
        //присваивание идентификатора схемы оформления
        $cells_array[$num_cell]['id'] = $id_color_scheme;
      }
    }
    $id_color_scheme++;
  }
  return $cells_array;
}


/**возвращает $cells_array в котором
  *объеденены соседние ячеки (в строках)
  *с одинаковыми цветовыми схемами*/
function horizontal_merge($cells_array, $rows_in_tab, $cols_in_tab){
  //вычисление количества ячеек
  $cells_count = $rows_in_tab * $cols_in_tab;

  //начальное количество объединений в ячейке
  $merge_count = 1;
  for ($cell_num_for_col = 1; $cell_num_for_col <= $cells_count; $cell_num_for_col++) {

    //начиная со второй ячейки в строке
    if ((($cell_num_for_col - 1) % $cols_in_tab) > 0) {

      //номер предыдущей ячейки с учетом объединения
      $prev_cell_num_for_col = $cell_num_for_col - $merge_count;

      // если в ячейку помещали данные
      if ($cells_array[$cell_num_for_col - $merge_count]['id'] != ''){

        //если цветовая схема ячейки равна с цветовой схемой предыдущей ячейки
        if ($cells_array[$prev_cell_num_for_col]['id'] == $cells_array[$cell_num_for_col]['id']) {

          //удалить ячейку
          unset($cells_array[$cell_num_for_col]);

          //объединениЙ в ячейке +1
          $cells_array[$prev_cell_num_for_col]['colspan'] = ++$merge_count;
        }
      }
    } else {
      //количество объединений в ячейке для следующей итерации
      $merge_count = 1;
    }
  }
  return $cells_array;
}

/**возвращает $cells_array в котором
  *объеденены соседние (в столбцах) ячеки
  *с одинаковыми схемами оформления
  *и количеством объединений (colspan)*/
function vertical_merge($cells_array, $rows_in_tab, $cols_in_tab){
  foreach ($cells_array as $cell_num_for_row => $cells_array_settings) {

    //начало сравнения с первой ячейки второй строки
    if ($cell_num_for_row > $cols_in_tab) {

        //обращение к предыдущим ячейкам в текущем столбце
        for ($row_num = 1; ($cell_num_for_row - ($cols_in_tab * $row_num)) > 0; $row_num++) {

        //индекс ячейки в предыдущей строке
        $check_cell = $cell_num_for_row - ($cols_in_tab * $row_num);

        //если ячеку с индексом $check_cell существует (т.е. ее не объединили)
        if (isset($cells_array[$check_cell])) {

          //если идентификаторы схем оформления равны
          //и если идентификатор цветовой схемы определен
          if (($cells_array_settings['id'] != '') &&
            ($cells_array_settings['id'] == $cells_array[$check_cell]['id']) &&
            ($cells_array_settings['colspan'] == $cells_array[$check_cell]['colspan'])) {

              //удалить ячейку
              unset($cells_array[$cell_num_for_row]);

              //объединение в столбце +1
              $cells_array[$check_cell]['rowspan']++;

              //прерывает цикл чтоб не было 2 объединения за одну итерацию
              break;
          } else {
            //прерывает цикл чтоб не объединялись через строку
            break;
          }
        }
      } //end for
    }
  } //end foreach
  return $cells_array;
}


/* генерирует HTML страницу с таблицей на основе данных в массиве $cells_array*/
function output_table($cells_array, $rows_in_tab, $cols_in_tab){

  //шапка HTML документа
  echo <<<_BEGIN
  <!DOCTYPE html>
  <html>
    <head>
      <meta name = "description" content = "Тестовое задание">
      <title>Itera PHP Test</title>
      <style type="text/css">
        table {
          border-collapse: collapse;
          border: 3px solid black;
        }
        td {
          font-family: Arial;
          font-weight: bold;
          font-size: 30px !important;
          border: 3px solid #000000;
        }
      </style>
    </head>
_BEGIN;

  //формирование таблицы
  echo <<<_JS_SCRIPT
  <body id="body">
    <table id="tab">
      <script type="text/javascript">

        //высота таблицы равна 99% от высоты внутреннего окна
        document.getElementById("tab").style.height=(window.innerHeight*0.99)+"px";

        //ширина таблицы равна высоте
        document.getElementById("tab").style.width=(window.innerHeight*0.99)+"px";
      </script>
      <tr>
_JS_SCRIPT;

  //счетчик строк
  $row_number = 1;

  //цикл по оставшимся после объединений ячейкам
  foreach ($cells_array as $cell_num_for_write => $settings_cell) {

    //индекс последней в строке ячейки
    $end_of_line = $row_number * $cols_in_tab;

    //расстановка тега конца/начала строки
    if ($cell_num_for_write > $end_of_line) {
      $next_row_number = intdiv($cell_num_for_write-1, $cols_in_tab) + 1;
      for ($row_number; $row_number < $next_row_number; $row_number++) {
          echo '</tr><tr>';
      }
    }
    //ширина ячейки в зависимости от объединений
    $cell_width = round(100/$cols_in_tab) * $settings_cell['colspan'];

    //высота ячейки в зависимости от объединений
    $cell_height = round(100/$rows_in_tab) * $settings_cell['rowspan'];

    //начало формирования ячейки
    echo "<td width=\"$cell_width%\" height=\"$cell_height%\"";
    foreach ($settings_cell as $cell_setting_name => $cell_setting_value) {
      if (($cell_setting_name != 'color') && ($cell_setting_name != 'text')){

        //параметры ячейки
        echo " $cell_setting_name=\"$cell_setting_value\"";

        //цвет текста
      } elseif ($cell_setting_name == 'color') {
        echo "><font color=\"$cell_setting_value\">";

        //вывод текста в ячейку
      } elseif ($cell_setting_name == 'text') {
        echo "$cell_setting_value<font></td>";
      }
    }
  }
  //конец HTML документа
  echo '</tr></table></body></html>';
}
