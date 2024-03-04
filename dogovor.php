
<?
require ("dbconnect.php");

require ("megatable.php");

if($_GET['print'] == 1) echo "<link href='css/litemgtable.css' rel='stylesheet'>";

class mgtable extends megatable  //Договорные документы
{

public function table_big_header() //заголовок таблицы
{
	echo "<tr>";
	echo "<td class = 'table_big_header' colspan = 999 align = center><div style='float:left;width:100%;text-align:center;'>" .$this->header_big_name ."</div><div class = 'mg_table_button'><div class = 'printer_layer'><a onclick = window.open('calendar.php','_blank')><img src='include/calendar.gif'></a> <a onclick = window.open('" .basename($_SERVER['PHP_SELF']) ."?print=1" .$_SERVER['QUERY_STRING'] ."','_blank','widht=600,height=800,location=no')><img src='include/Printer.png' alt='закрыто'></a></div> <div id = 'eco_pass_field'><a onclick = 'open_eco_edit();'><img src='include/lock_large_locked.png' alt='закрыто'></a></div></div></td>"; //малый заголовок
	echo "</tr>";
}

public function nomeric_col($r,$f,$i,$j,$pos_nomer_col) //колонка нумерации строк
{
	if(!isset($j) and $i == $pos_nomer_col) print "<td class = 'table_header " .$this->nomer_col_style ."' style = 'width:0px;'></td>";  //заголовок
	if(isset($j) and $i == $pos_nomer_col) print "<td data-id='" .$f['id'] ."' data-table='" .$this->dbtable ."' data-actfile='" .basename($_SERVER['PHP_SELF']) ."' data-htag='" .$this->htag ."' data-getdata ='" .$this->getdata ."' class = 'table_header meganomer " .$this->nomer_col_style ."'></td>";  //колонка
}

	public function add_col($r,$f,$i,$j)
{
	require ("global_path.php");

	
	//Доп колонка Обоснование трудоемкости
	if(!isset($j) and $i == 10) print "<td class = 'table_header' style = 'width:180px;'>Обоснование трудоемкости</td>";  //заголовок
	if(isset($j) and $i == 10) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'dogovor' and detid = '" .$f['id'] ."' and type = 1 ORDER BY " .$this->sortirovka);
	
		echo "<td class = 'table_field'>";

		for ($ii=0; $ii<mysql_num_rows($rr); $ii++) //чтение строк
		{
			$ff=mysql_fetch_array($rr);
			$full_link_path = "http://" .$_SERVER['SERVER_NAME'] .$save_dir .$ff['local_path'] ."/" .$ff['prefix'] ."_";
			
			$file_icon = $this->icon_file($ff['filename']);
		
			echo "<a href = '".$full_link_path .$ff['filename'] ."' target = '_blank'><img src= 'include/" .$file_icon ."' title = '" .$ff['maskname'] ."'></a>";
			echo " ";
		}
		echo "<div class = 'button_field button_layer' style = 'display:none;'>";
			echo "<a onClick = okno_show('dialog','&tabname=" .$this->dbtable ."&type=1&id=" .$f['id'] ."');><img src = 'include/new window.png' title = ''></a>";
		echo "</div>";

		echo "</td>";  //колонка
		
	}

	//Доп колонка Обоснование трудоемкости
	if(!isset($j) and $i == 11) print "<td class = 'table_header' style = 'width:180px;'>Ведомость исполнения</td>";  //заголовок
	if(isset($j) and $i == 11) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'dogovor' and detid = '" .$f['id'] ."' and type = 2 ORDER BY " .$this->sortirovka);
	
		echo "<td class = 'table_field'>";

		for ($ii=0; $ii<mysql_num_rows($rr); $ii++) //чтение строк
		{
			$ff=mysql_fetch_array($rr);
			$full_link_path = "http://" .$_SERVER['SERVER_NAME'] .$save_dir .$ff['local_path'] ."/" .$ff['prefix'] ."_";
			
			$file_icon = $this->icon_file($ff['filename']);
		
			echo "<a href = '".$full_link_path .$ff['filename'] ."' target = '_blank'><img src= 'include/" .$file_icon ."' title = '" .$ff['maskname'] ."'></a>";
			echo " ";
		}
		echo "<div class = 'button_field button_layer' style = 'display:none;'>";
			echo "<a onClick = okno_show('dialog','&tabname=" .$this->dbtable ."&type=2&id=" .$f['id'] ."');><img src = 'include/new window.png' title = ''></a>";
		echo "</div>";

		echo "</td>";  //колонка
		
	}
	
}

public function under_row($r,$f,$j)
{
	if(isset($f['id'])) {
	echo "<tr><td class = '' colspan = 999 align = left>";
	echo "<input type='checkbox' name='checkdogvpmo' class = 'checkfield' data-id = '" .$f['id'] ."_controlvpmo_dogovor' data-chk = '0' disabled>проект под контролем ВП МО РФ ";
	echo "<input type='checkbox' name='chkdogopen' class = 'checkfield' data-id = '" .$f['id'] ."_dogopen_dogovor' data-chk = '0' disabled> договор заключен ";
	echo "<input type='checkbox' name='chkdogclose' class = 'checkfield' data-id = '" .$f['id'] ."_dogclose_dogovor' data-chk = '0' disabled> договор закрыт ";
	echo "</td></tr>";
	if ($f['controlvpmo'] == "1") echo "<script>$('[name=checkdogvpmo]').attr('checked','').attr('data-chk','1');</script>";
	if ($f['dogopen'] == "1") echo "<script>$('[name=chkdogopen]').attr('checked','').attr('data-chk','1');</script>";
	if ($f['dogclose'] == "1") echo "<script>$('[name=chkdogclose]').attr('checked','').attr('data-chk','1');</script>";
	}
}

}

class mgtablezakaz extends megatable
{
	public function nomeric_col($r,$f,$i,$j,$pos_nomer_col) //колонка нумерации строк
	{
		if(!isset($j) and $i == $pos_nomer_col) print "<td class = 'table_header " .$this->nomer_col_style ."' style = 'width:0px;'></td>";  //заголовок
		if(isset($j) and $i == $pos_nomer_col) print "<td data-id='" .$f['id'] ."' data-table='" .$this->dbtable ."' data-actfile='" .basename($_SERVER['PHP_SELF']) ."' data-htag='" .$this->htag ."' data-getdata ='" .$this->getdata ."' class = 'table_header meganomer " .$this->nomer_col_style ."'></td>";  //колонка
	}
}


class mgtablestage extends megatable //Этапы договора
{

	public function dateconv($dt)
{
	return " CONCAT(SUBSTRING_INDEX(" .$dt .",'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(" .$dt .",'.',2),'.',-1), SUBSTRING_INDEX(" .$dt .",'.',1)) ";
}

	public function add_col($r,$f,$i,$j)
{
	require ("global_path.php");

	
	//Доп колонка Отчётные документы по этапу
	if(!isset($j) and $i == 8) print "<td class = 'table_header' style = 'width:180px;'>Отчётные документы по этапу</td>";  //заголовок
	if(isset($j) and $i == 8) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'stage' and detid = '" .$f['id'] ."' and type = 1 ORDER BY " .$this->sortirovka);
	
		echo "<td class = 'table_field'>";

		for ($ii=0; $ii<mysql_num_rows($rr); $ii++) //чтение строк
		{
			$ff=mysql_fetch_array($rr);
			$full_link_path = "http://" .$_SERVER['SERVER_NAME'] .$save_dir .$ff['local_path'] ."/" .$ff['prefix'] ."_";
			
			$file_icon = $this->icon_file($ff['filename']);
		
			echo "<a href = '".$full_link_path .$ff['filename'] ."' target = '_blank'><img src= 'include/" .$file_icon ."' title = '" .$ff['maskname'] ."'></a>";
			echo " ";
		}
		echo "<div class = 'button_field button_layer' style = 'display:none;'>";
			echo "<a onClick = okno_show('dialog','&tabname=" .$this->dbtable ."&type=1&id=" .$f['id'] ."');><img src = 'include/new window.png' title = ''></a>";
		echo "</div>";

		echo "</td>";  //колонка
		
	}

	//Доп колонка Трудоемкость
	if(!isset($j) and $i == 11) print "<td class = 'table_header' style = 'width:40px;'>Фактическая трудоемкость н/ч</td>";  //заголовок
	if(isset($j) and $i == 11) 
	{

		echo "<td class = 'table_field'>";

		//echo $f['datestart'] ." " .$f['datestop'] ." " .$f['dateprobablestop'] ." " .$f['detid'];
		if($f['datestart'] != "" and $f['datestop'] != "")
		{
			$sql_date_field = $this->dateconv("docwork.date");
			$sql_date_begin = $this->dateconv("'" .$f['datestart'] ."'");
			$sql_date_end = $this->dateconv("'" .$f['datestop'] ."'");
			$sql_date_endpb = $this->dateconv("'" .$f['dateprobablestop'] ."'");
	
			$r_date_stage = mysql_query("SELECT * FROM docwork where id >= 0 and hide <> 1 and detid = '" .$f['detid'] ."' and " .$sql_date_field ." >= " .$sql_date_begin ." and " .$sql_date_field ." <= " .($f['dateprobablestop'] == "" ? $sql_date_end : $sql_date_endpb) ." ");
	
			for ($ii=0; $ii<mysql_num_rows($r_date_stage); $ii++) //чтение строк
			{
				$f_date_stage=mysql_fetch_array($r_date_stage);
				$all_normachas += $f_date_stage['sumnormachas'];
			}

			$sql_date_field = $this->dateconv("mailbox.datevh");
			$r_date_mailbox = mysql_query("SELECT * FROM mailbox where id >= 0 and hide <> 1 and detid = '" .$f['detid'] ."' and " .$sql_date_field ." >= " .$sql_date_begin ." and " .$sql_date_field ." <= " .($f['dateprobablestop'] == "" ? $sql_date_end : $sql_date_endpb) ." ");
			for ($ii=0; $ii<mysql_num_rows($r_date_mailbox); $ii++) //чтение строк
			{
				$f_date_mailbox=mysql_fetch_array($r_date_mailbox);
				@$all_normachas_vh += $f_date_mailbox['sumnormchasvh'];
			}

			$sql_date_field = $this->dateconv("mailbox.dateish");
			$r_date_mailbox = mysql_query("SELECT * FROM mailbox where id >= 0 and hide <> 1 and detid = '" .$f['detid'] ."' and " .$sql_date_field ." >= " .$sql_date_begin ." and " .$sql_date_field ." <= " .($f['dateprobablestop'] == "" ? $sql_date_end : $sql_date_endpb) ." ");
			for ($ii=0; $ii<mysql_num_rows($r_date_mailbox); $ii++) //чтение строк
			{
				$f_date_mailbox=mysql_fetch_array($r_date_mailbox);
				@$all_normachas_ish += $f_date_mailbox['sumnormchasish'];
			}

		}
		
		echo $all_normachas == "" ? "Док:0" : "Док:" .$all_normachas;
		if($all_normachas_vh != "") echo "<br>Вх:" .$all_normachas_vh;
		if($all_normachas_ish != "") echo "<br>Исх:" .$all_normachas_ish;
		echo "<br>(" .($all_normachas + $all_normachas_vh + $all_normachas_ish) .")";
		
		echo "</td>";  //колонка
		
	}

}
}

class mgplantruda extends megatable
{

public function hide_field($r,$i)  //функция сокрытия полей в соответствии с заданными условиями hidefield в settings
{
	$r_settings = mysql_query("SELECT * FROM settings where tablename = 'plantruda' and field = '" .mysql_field_name($r, $i) ."' ");
	$f_settings = mysql_fetch_array($r_settings);

	if ($f_settings['hidefield'] == "1") return true;
}

public function table_header($r) //заголовок таблицы
{
	for ($i=0; $i<mysql_num_fields($r); $i++)
	{
		$this->all_col($r,$f,$i,$j);
		if($this->hide_field($r,$i)) continue;

		$r_settings = mysql_query("SELECT * FROM settings where tablename = 'plantruda' and field = '" .mysql_field_name($r, $i) ."'");
		$f_settings = mysql_fetch_array($r_settings);
		if($f_settings['showname']) echo "<td class = 'table_header' style = 'width:" .$f_settings['size'] ."'>" .$f_settings['showname'] ."</td>";
		else echo "<td class = 'table_header'>" .mysql_field_name($r, $i) ."</td>";
	}
}

public function table_big_header() //заголовок таблицы
{
	echo "<tr>";
	echo "<td class = 'table_big_header' colspan = 999 align = center>" .$this->header_big_name ."</td>"; //малый заголовок
	echo "</tr>";
	echo "<tr>";
	echo "<td class = 'table_colspan_header' colspan = 2 align = center></td>";
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>31 отдел</td>"; //малый заголовок
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>32 отдел</td>";
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>33 отдел</td>";
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>34 отдел</td>";
	echo "<td class = 'table_colspan_header' colspan = 2 align = center>35 отдел</td>";
	echo "</tr>";
}

}

class mgtabletz extends megatable
{
	public function add_col($r,$f,$i,$j)
{
	require ("global_path.php");

	
	//Доп колонка Ссылка на скан документа
	if(!isset($j) and $i == 7) print "<td class = 'table_header' style = 'width:180px;'>Ссылка на скан документа</td>";  //заголовок
	if(isset($j) and $i == 7) 
	{

		$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'techzadanie' and detid = '" .$f['id'] ."' and type = 1 ORDER BY " .$this->sortirovka);
	
		echo "<td class = 'table_field'>";

		for ($ii=0; $ii<mysql_num_rows($rr); $ii++) //чтение строк
		{
			$ff=mysql_fetch_array($rr);
			$full_link_path = "http://" .$_SERVER['SERVER_NAME'] .$save_dir .$ff['local_path'] ."/" .$ff['prefix'] ."_";
			
			$file_icon = $this->icon_file($ff['filename']);
		
			echo "<a href = '".$full_link_path .$ff['filename'] ."' target = '_blank'><img src= 'include/" .$file_icon ."' title = '" .$ff['maskname'] ."'></a>";
			echo " ";
		}
		echo "<div class = 'button_field button_layer' style = 'display:none;'>";
			echo "<a onClick = okno_show('dialog','&tabname=" .$this->dbtable ."&type=1&id=" .$f['id'] ."');><img src = 'include/new window.png' title = ''></a>";
		echo "</div>";

		echo "</td>";  //колонка
		
	}
}

}


$dtable = new mgtable;

$dtable->htag = "varframe";
$dtable->addfield = "detid";
$dtable->addvalue = $_GET['id'];
$dtable->getdata = "&id=" .$_GET['id'];
//$dtable->getdata = $_SERVER['QUERY_STRING'];
$dtable->filter = " and detid = " .$_GET['id'];

$dtable->dbtable = "dogovor";
$dtable->header_big_name = "Договорные документы";
$dtable->bd_query_text();
$dtable->tabposcount >= 1 ? $dtable->show_add_db_button = 0 : $dtable->show_add_db_button = 1;
$dtable->pos_nomer_col = 0;
$dtable->date_field_list = "date";
//$dtable->nomer_col_style = "hide_field";
$dtable->datatable();
echo "<br>";
echo "<br>";
//***************************************


$dtabzakaz = new mgtablezakaz;

$dtabzakaz->htag = "varframe";
$dtabzakaz->addfield = "detid";
$dtabzakaz->addvalue = $_GET['id'];
//$dtable->getdata = "&id=" .$_GET['id'];
$dtabzakaz->getdata = $_SERVER['QUERY_STRING'];
$dtabzakaz->filter = " and detid = " .$_GET['id'];

$dtabzakaz->dbtable = "zakaz";
$dtabzakaz->header_big_name = "Заказ";
$dtabzakaz->bd_query_text();
$dtabzakaz->tabposcount >= 1 ? $dtabzakaz->show_add_db_button = 0 : $dtabzakaz->show_add_db_button = 1;
$dtabzakaz->pos_nomer_col = 0;
$dtabzakaz->date_field_list = "date";
//$dtabzakaz->nomer_col_style = "hide_field";
//$dtabzakaz->datatable();
//echo "<br>";
//echo "<br>";
//***************************





$dtablestage = new mgtablestage;

$dtablestage->htag = "varframe";
$dtablestage->addfield = "detid";
$dtablestage->addvalue = $_GET['id'];
//$dtable->getdata = "&id=" .$_GET['id'];
$dtablestage->getdata = $_SERVER['QUERY_STRING'];
$dtablestage->filter = " and detid = " .$_GET['id'];

$dtablestage->dbtable = "stage";
$dtablestage->header_big_name = "Этапы договора";
$dtablestage->show_add_db_button = 1;
$dtablestage->pos_nomer_col = 0;
$dtablestage->date_field_list = "datestart,datestop,dateprobablestop";
$dtablestage->nomer_col_style = "";
$dtablestage->datatable();
echo "<br>";
echo "<br>";
//**********************************

$dtplantruda = new mgplantruda;
$dtplantruda->htag = "varframe";
$dtplantruda->addfield = "detid";
$dtplantruda->addvalue = $_GET['id'];
//$dtable->getdata = "&id=" .$_GET['id'];
$dtplantruda->getdata = $_SERVER['QUERY_STRING'];
$dtplantruda->filter = " and detid = " .$_GET['id'];

$dtplantruda->dbtable = "stage";
$dtplantruda->header_big_name = "Планируемая трудоёмкость отделов КК-3";
$dtplantruda->pos_nomer_col = 0;
$dtplantruda->nomer_col_style = "";
$dtplantruda->datatable();
echo "<br>";
echo "<br>";
//**********************************

$dtabletz = new mgtabletz;

$dtabletz->htag = "varframe";
$dtabletz->addfield = "detid";
$dtabletz->addvalue = $_GET['id'];
//$dtable->getdata = "&id=" .$_GET['id'];
$dtabletz->getdata = $_SERVER['QUERY_STRING'];
$dtabletz->filter = " and detid = " .$_GET['id'];

$dtabletz->dbtable = "techzadanie";
$dtabletz->header_big_name = "Договор и техническое задание";
$dtabletz->show_add_db_button = 1;
$dtabletz->pos_nomer_col = 0;
$dtabletz->date_field_list = "date";
$dtabletz->nomer_col_style = "";
$dtabletz->datatable();

//if($_GET['show_button_field'] == 1) echo "<script>open_edit();</script>";
?>
<script>
//console.log("end doc");
function chkzamok() {
//console.log("chkzamok1: " + zamok);
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
}
chkzamok();

//function upd_chk() {
$('[name="checkdogvpmo"]').change(function() {
//console.log("upd_box: " + $(this).data('id') + " chk: " + $(this).prop('checked'));
if ($(this).prop('checked') == true) {update_db($(this).data('id'), '1');}
if ($(this).prop('checked') == false) {update_db($(this).data('id'), '0');}
});

$('[name="chkdogopen"]').change(function() {
//console.log("upd_box: " + $(this).data('id') + " chk: " + $(this).prop('checked'));
if ($(this).prop('checked') == true) {update_db($(this).data('id'), '1');}
if ($(this).prop('checked') == false) {update_db($(this).data('id'), '0');}
});

$('[name="chkdogclose"]').change(function() {
//console.log("upd_box: " + $(this).data('id') + " chk: " + $(this).prop('checked'));
if ($(this).prop('checked') == true) {update_db($(this).data('id'), '1');}
if ($(this).prop('checked') == false) {update_db($(this).data('id'), '0');}
});

$(document).ready(function() {
//console.log("chkzamok2: " + zamok);
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}

if (x_ecopass != 0)	{
	$('#eco_pass_field').empty()
	.append("<a onclick = 'close_edit();'><img src='include/lock_large_unlocked.png' alt='закрыто'>");
	//console.log("eco_pass_field");
}


});

</script>