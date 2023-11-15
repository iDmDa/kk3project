<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Table</title>
    </head>

<script>activefile = "<? echo basename($_SERVER['PHP_SELF']); ?>";</script>
<?

class megatable
{
public $field ="*";  //поля считываемые из базы данных
public $dbtable; // = "incoming";  //название таблицы в базе данных
public $filter = "";  // фильтр по базе данных
public $hfilter;  //фильтр для поля hide
public $show_hide = 0;  // 0 - использовать стандартное правило $hfilter, 1 - показывать все
public $sortirovka = "id"; //сортировка по полю
public $show_line = 0;
public $max_line = 1000; //число строк на с транице
public $max_pages; //расчетное число страниц
public $show_page = -1; //показать страницу (если -1, то последнюю)

public $htag;  // обязательное поле - div поле в которое производится загрзка/обновление таблицы
public $page_id; //id поля на которое сгенерирована страница

public $pos_nomer_col = -1; //положение стандартной колонки нумерация (после какого поля поставить)
public $nomer_col_style; //стиль цифровой колонки
public $nomer_col_menu_style = " meganomer "; //стиль контекстного меню цифровой колонки
public $date_field_list; //имя дата полей (через запятую)

public $show_javafunc = 0; // 0/1 - подключить/отключить javascript функции
public $filename; //при использовании $show_javafunc - указать файл php для загрузки, по умолчанию вызывает сам себя

public $show_add_db_button = 0; // 0/1 - скрыть/показать кнопку добавить строку. использовать при включении $show_javafunc
public $addfield = ""; //для кнопки 'добавить' - поле базы данных в которое внести запись(создание новой записи)
public $addvalue = ""; //для кнопки 'добавить' значение поля $addfield

public $getdata = ""; //для функции refresh, GET данные необходимые для загрузки страниц

public $table_id = ""; //id имя таблицы
public $table_class = "autotable"; //class имя таблицы
public $table_other_attr = ""; //"contenteditable='true'";  //доп атрибуты таблицы
public $show_big_header = 1;  // 1 - поставить большой заголовок таблицы во всю ширину
public $header_big_name = "Имя"; //текст заголовка таблицы
public $tabposcount; //количество строк в таблице

public function var_separate($var_list, $separate_item)  
//$var_list - список переменных, записанных через разделитель вида $var_list = "один|два|три|"
//$separate_item - знак разделитель использованный в списке н: "|" или "," и т.п.
//$single_var - итоговая переменная массив, считает с 0, использовать как $single_var[4]
{
	return explode($separate_item, $var_list);
}

public function icon_file($filename)  
{
		$file_ext = $this->var_separate($filename, ".");

		switch ($file_ext[count($file_ext) - 1])
		{
			case "doc":
			$file_icon = "word_ico.png";
			break;

			case "docx":
			$file_icon = "word_ico.png";
			break;

			case "txt":
			$file_icon = "txt_ico.png";
			break;
		
			case "pdf":
			$file_icon = "pdf_ico.png";
			break;

			case "jpg":
			$file_icon = "jpg-ico.png";
			break;

			case "png":
			$file_icon = "png-ico.png";
			break;

			case "zip":
			$file_icon = "zip-ico.png";
			break;

			case "rar":
			$file_icon = "zip-ico.png";
			break;

			default:
			$file_icon = "unknow_ico.png";
			break;
		}

	return $file_icon;
}

public function hide_field($r,$i)  //функция сокрытия полей в соответствии с заданными условиями hidefield в settings
{
	$r_settings = mysql_query("SELECT * FROM settings where tablename = '" .$this->dbtable ."' and field = '" .mysql_field_name($r, $i) ."' ");
	$f_settings = mysql_fetch_array($r_settings);

	if ($f_settings['hidefield'] == "1") return true;
}

public function bd_query_text()
{
	if($this->filename == "") $this->filename = basename($_SERVER['PHP_SELF']); // обновить файл из которого вызван
	if($this->show_hide == 0) $this->hfilter = " and hide <> 1 ";
	$dbquery = "SELECT " .$this->field ." FROM " .$this->dbtable ." where id >= 0 " .$this->hfilter .$this->filter ." ORDER BY " .$this->sortirovka;
	//$this->tabposcount = mysql_num_rows(mysql_query($dbquery));
	$this->pages_rule($dbquery);
	$dbquery .= " LIMIT " .$this->show_line .", " .$this->max_line;
	return $dbquery;
}

public function pages_rule($dbquery)
{
	$this->tabposcount = mysql_num_rows(mysql_query($dbquery));
	$this->max_pages = ceil($this->tabposcount / $this->max_line);
	if($this->show_page == -1) $this->show_page = $this->max_pages;
	if($this->show_page == 0) {$this->show_line = 0; $this->max_line = 1000; return;}
	if($this->max_pages > 0) $this->show_line = ($this->show_page - 1) * $this->max_line;
}

public function pages_buttons()
{
	echo "<br><table id = 'pagesfield'><tr>";
	for ($i=1; $i<$this->max_pages + 1; $i++)
	{
		if($this->show_page == $i) echo "<td><div class = 'menuitem menuactive' onClick=refresh('" .$this->filename ."','" .$this->htag ."','" .$this->getdata ."&page=" .$i ."');> " .$i ." </div></td>";
		else echo "<td><div class = 'menuitem' onClick=refresh('" .$this->filename ."','" .$this->htag ."','" .$this->getdata ."&page=" .$i ."');> " .$i ." </div></td>";
	}
		if($this->show_page == 0) echo "<td><div class = 'menuitem menuactive' onClick=refresh('" .$this->filename ."','" .$this->htag ."','" .$this->getdata ."&page=0');> Всё </div></td>";
		else echo "<td><div class = 'menuitem' onClick=refresh('" .$this->filename ."','" .$this->htag ."','" .$this->getdata ."&page=0');> Всё </div></td>";
	echo "</tr></table>";
	echo "<br><br>";
}

public function under_row($r,$f,$j)
{

}

public function add_col($r,$f,$i,$j)
{

}

public function nomeric_col($r,$f,$i,$j,$pos_nomer_col) //колонка нумерации строк
{
	if(!isset($j) and $i == $pos_nomer_col) print "<td class = 'table_header" .$this->nomer_col_style ."' style = 'width:25px;'>№</td>";  //заголовок
	if(isset($j) and $i == $pos_nomer_col) print "<td data-id='" .$f['id'] ."' data-table='" .$this->dbtable ."' data-actfile='" .basename($_SERVER['PHP_SELF']) ."' data-htag='" .$this->htag ."' data-getdata ='" .$this->getdata ."' class = 'table_nomeric_col " .$this->nomer_col_menu_style .$this->nomer_col_style ."'>" .($j+1) ."</td>";  //колонка
}

public function standart_col($r,$f,$i,$j)
{
	$this->nomeric_col($r,$f,$i,$j,$this->pos_nomer_col);
}

public function all_col($r,$f,$i,$j)
{
	$this->standart_col($r,$f,$i,$j);
	$this->add_col($r,$f,$i,$j);
}

public function table_big_header() //заголовок таблицы
{
	echo "<tr>";
	echo "<td class = 'table_big_header' colspan = 999 align = center>" .$this->header_big_name ."</td>"; //малый заголовок
	echo "</tr>";
}

public function table_header($r) //заголовок таблицы
{
	for ($i=0; $i<mysql_num_fields($r); $i++)
	{
		$this->all_col($r,$f,$i,$j);
		if($this->hide_field($r,$i)) continue;

		$r_settings = mysql_query("SELECT * FROM settings where tablename = '" .$this->dbtable ."' and field = '" .mysql_field_name($r, $i) ."'");
		$f_settings = mysql_fetch_array($r_settings);
		if($f_settings['showname']) echo "<td class = 'table_header' style = 'width:" .$f_settings['size'] ."'>" .$f_settings['showname'] ."</td>";
		else echo "<td class = 'table_header'>" .mysql_field_name($r, $i) ."</td>";
	}
}

public function table_content($r,$f,$j)
{
	for ($i=0; $i<mysql_num_fields($r); $i++) // чтение значений в строке
	{
		if ($i==0) echo "<tr>";
		$this->all_col($r,$f,$i,$j);
		if($this->hide_field($r,$i)) continue;
		$this->date_field_check($r,$f,$j,$i) ? $this->date_field($r,$f,$j,$i) : $this->normal_field($r,$f,$j,$i);
		//$this->date_field($r,$f,$j,$i);
	}
}

public function normal_field($r,$f,$j,$i)
{
	print "<td id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class = 'simplefield'>" .$f[$i] ."</td>"; //заполнение полей (ячейки в строке)
}

public function date_field($r,$f,$j,$i)
{
	print "<td style='width:70px'><input type='text' id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class='dateinput' onchange='update_db(this.id,this.value);' value='" .$f[$i] ."'></td>"; //заполнение полей (ячейки в строке)
}

public function date_field_check($r,$f,$j,$i)
{
	//date_field_list = "datevh,dateish";
	$datelist = $this->var_separate($this->date_field_list, ",");
	for($n=0;$n<count($datelist);$n++) if (mysql_field_name($r, $i) == $datelist[$n]) return true;
}

public function javascript_functions($dbtable) //генерация именных javascript функций
{
if($this->filename == "") $this->filename = basename($_SERVER['PHP_SELF']); // обновить файл из которого вызван
?>
	<script>

	</script>
<?
}

public function doc_ready()  //javascript функции исполняемые после загрузки страницы
{
?>
	<script>
$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
	</script>
<?
}

public function add_db_button($dbtable, $field, $fieldvalue, $htag, $getdata) //добавить кнопку добавления поля
{
	if($this->filename == "") $this->filename = basename($_SERVER['PHP_SELF']); // обновить файл из которого вызван
	echo "<div class = 'button_field' style = 'display:none;'>";
//add_refresh(table, field, fieldvalue, loadfile, htag, getdata) 
	echo "<a onclick=add_refresh('" .$dbtable ."','" .$field ."','" .$fieldvalue ."','" .$this->filename ."','" .$htag ."','" .$getdata ."');><img src = 'include/addline.png'></a>"; 
	echo "</div>";
}

public function delete_db_button($dbtable, $htag, $id, $add_data) //добавить кнопку добавления поля
{
	echo "<div class = 'button_field' style = 'display:none;padding: 0px;'>";
	echo "<a onclick=delete_refresh_" .$dbtable ."('#" .$htag ."','" .$dbtable ."','" .$id ."','" .$add_data ."');><img src = 'include/delete.png'></a>"; 
	echo "</div>";
}

public function datatable()
{

	if(!$this->show_javafunc == 0) $this->javascript_functions($this->dbtable);	

	$r=mysql_query($this->bd_query_text());
	
	print "<table id = '" .$this->table_id ."' class = '" .$this->table_class ."' " .$this->table_other_attr .">";
	
	//заполнение таблицы данными
	mysql_num_rows($r) == 0 ? $empty_header = 1 : $empty_header = 0;
	for ($j=0; $j<mysql_num_rows($r) + $empty_header; $j++) //чтение строк
	{
		$f=mysql_fetch_array($r);
		if($j==0) echo "<thead class = 'table_header_block'>";
		if($j==0 and $this->show_big_header==1) $this->table_big_header();
		if($j==0) $this->table_header($r);
		if($j==0) echo "</thead>";
		if($j==0) echo "<tbody class = 'table_body_block'>";
		if($empty_header != 1) $this->table_content($r,$f,$j);

		$this->under_row($r,$f,$j);
	}

 	print "</tbody></table>";

	if(!$this->show_add_db_button == 0) $this->add_db_button($this->dbtable, $this->addfield, $this->addvalue, $this->htag, $this->getdata);
	if($this->max_pages > 1) $this->pages_buttons();
	//if(!$this->show_javafunc == 0) 
	$this->doc_ready();
}

} //megatable

/*
class mgtable extends megatable
{
	public function add_col($r,$f,$i,$j)
{
	if(!isset($j) and $i == 3) print "<td class = 'table_header'>Новое</td>";  //заголовок
	if(isset($j) and $i == 3) print "<td>" .($j+1) ."</td>";  //колонка
}
}
*/

?>
</html>




