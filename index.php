<?PHP
//начальные установки
require_once('config.php');

//подключение библиотеки проекта
require_once('test_table_libruary.php');

//входящий массив
$start_array = array(
      array('text' => 'Текст красного цвета',
            'cells' => '1,2,4,5',
            'align' => 'center',
            'valign' => 'center',
            'color' => 'FF0000',
            'bgcolor' => '0000FF'),
      array('text' => 'Текст зеленого цвета',
            'cells' => '8,9',
            'align' => 'right',
            'valign' => 'bottom',
            'color' => '00FF00',
            'bgcolor' => 'FFFFFF')
      );


merge_cells_in_table($start_array);
