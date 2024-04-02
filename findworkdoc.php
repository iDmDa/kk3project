
<?

require ("megatable.php");
if($_GET['print'] == 1) echo "<link href='css/litemgtable.css' rel='stylesheet'>";
class mgtable extends megatable
{

	public function table_big_header1() //заголовок таблицы
	{
		echo "<tr>";
		echo "<td class = 'table_big_header' colspan = 999 align = center><div style='float:left;width:100%;text-align:center;'>" .$this->header_big_name ."</div><div class = 'mg_table_button'><div class = 'printer_layer'><a onclick = window.open('" .basename($_SERVER['PHP_SELF']) ."?print=1" .$_SERVER['QUERY_STRING'] ."','Печать','widht=600,height=800,location=no');document.poisk.submit();><img src='include/Printer.png' alt='закрыто'></a></div></div></td>"; //малый заголовок
		echo "</tr>";
	}

	public function table_content($r,$f,$j)
	{
		for ($i=0; $i<mysql_num_fields($r); $i++) // чтение значений в строке
		{
			if ($i==0) 
			{
				if($f['gotovnost'] == "100") echo "<tr class = 'tab_line_ready'>";
				else {
						if(strtotime(date("d.m.Y")) > strtotime($f['date']) and $f['date'] != "") echo "<tr class = 'tab_line_notready'>";
						else echo "<tr>";
					}
			}
			$this->all_col($r,$f,$i,$j);
			if($this->hide_field($r,$i)) continue;
			$this->date_field_check($r,$f,$j,$i) ? $this->date_field($r,$f,$j,$i) : $this->normal_field($r,$f,$j,$i);
			//$this->date_field($r,$f,$j,$i);
		}
	}

	public function hide_field($r,$i)  //функция сокрытия полей в соответствии с заданными условиями hidefield в settings
	{
		$r_settings = mysql_query("SELECT * FROM settings where tablename = 'findworkdoc' and field = '" .mysql_field_name($r, $i) ."' ");
		$f_settings = mysql_fetch_array($r_settings);

		if ($f_settings['hidefield'] == "1") return true;
	}

	public function normal_field($r,$f,$j,$i)
	{
		if(mysql_field_name($r, $i) == 'detid') 
		{
			$r_detid = mysql_query("SELECT * FROM izdelie where id = '" .$f[$i] ."' ");
			$f_detid = mysql_fetch_array($r_detid);
			print "<td id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class = 'notsimplefield'>" .$f_detid['name'] ."</td>"; 
		}
		else print "<td id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class = 'simplefield'>" .$f[$i] ."</td>"; 
	}


		public function add_col($r,$f,$i,$j)
	{
		require ("global_path.php");

		
		//Доп колонка Черновик
		if(!isset($j) and $i == 14) print "<td class = 'table_header' style = 'width:70px;'>Черно-<br>вик</td>";  //заголовок
		if(isset($j) and $i == 14) 
		{

			$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'docwork' and detid = '" .$f['id'] ."' and type = 1 ORDER BY id");
		
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

		//Доп колонка Сканированный
		if(!isset($j) and $i == 14) print "<td class = 'table_header' style = 'width:70px;'>Скани-<br>рован-<br>ный</td>";  //заголовок
		if(isset($j) and $i == 14) 
		{

			$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'docwork' and detid = '" .$f['id'] ."' and type = 2 ORDER BY id");
		
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
}

class mgtablestage extends megatable
{

	public function bd_query_text()
	{
		if($this->filename == "") $this->filename = basename($_SERVER['PHP_SELF']); // обновить файл из которого вызван
		if($this->show_hide == 0) $this->hfilter = " and hide <> 1 ";
		//$dbquery = "SELECT " .$this->field ." FROM " .$this->dbtable ." where id >= 0 " .$this->hfilter .$this->filter ." ORDER BY " .$this->sortirovka;
		$dbquery = " select stage.* from stage left join izdelie on izdelie.id = stage.detid where izdelie.hide <> 1 and stage.hide <> 1 " .$this->filter ." ORDER BY " .$this->sortirovka;
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

	public function hide_field($r,$i)  //функция сокрытия полей в соответствии с заданными условиями hidefield в settings
	{
		$r_settings = mysql_query("SELECT * FROM settings where tablename = 'findstage' and field = '" .mysql_field_name($r, $i) ."' ");
		$f_settings = mysql_fetch_array($r_settings);

		if ($f_settings['hidefield'] == "1") return true;
	}

	public function normal_field($r,$f,$j,$i)
	{
		if(mysql_field_name($r, $i) == 'detid') 
		{
			$r_detid = mysql_query("SELECT * FROM izdelie where id = '" .$f[$i] ."' ");
			$f_detid = mysql_fetch_array($r_detid);
			print "<td id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class = 'notsimplefield'>" .$f_detid['name'] ."</td>"; 
		}
		else print "<td id = '" .$f['id'] ."_" .mysql_field_name($r, $i) ."_" .$this->dbtable ."' class = 'simplefield ыыы'>" .$f[$i] ."</td>"; 
	}

	public function add_col($r,$f,$i,$j)
	{
		require ("global_path.php");

			//Доп колонка договор заключен
		if(!isset($j) and $i == 3) print "<td class = 'table_header' style = 'width:180px;'>Договор заключен</td>";  //заголовок
		if(isset($j) and $i == 3) 
		{

			$rr = mysql_query("SELECT * FROM dogovor where detid = '" .$f['detid'] ."' ");
			$ff = mysql_fetch_array($rr);

			echo "<td class = 'table_field' align = center>";
			
			echo $ff['dogopen'] == 1 ? "<input type='checkbox' checked disabled>" : "<input type='checkbox'  disabled>";
			//echo $f['detid'];

			echo "</td>";  //колонка
			
		}


		//Доп колонка Отчётные документы по этапу
		if(!isset($j) and $i == 8) print "<td class = 'table_header' style = 'width:180px;'>Отчётные документы по этапу</td>";  //заголовок
		if(isset($j) and $i == 8) 
		{

			$rr = mysql_query("SELECT * FROM uplfiles where id >= 0 and hide <> 1 and tabname = 'stage' and detid = '" .$f['id'] ."' and type = 1 ORDER BY id");
		
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

class mgtabledog extends megatable  //Договорные документы
{

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
}

//border: none; outline: none;background:inherit; - невидимое поле
echo "<div id = 'findblock'>";
if ($_GET['print'] == 1) echo "<form name='poisk' action='findworkdoc.php' target='newwin' method='post'>";
echo "<table class = 'autotable razdel'><tr><td class = 'table_header' colspan = 999 align = center>Документы и работа</td></tr></table>";
echo "<div id = 'poiskdocwork' class = '' style = 'display:block;'>";
echo "<p>Наименование документа или работы: <input type='text' class='findnaimenovenie'> Дата выпуска с <input id = 'finddatebegin' type='text'  style='width:70px;' class=''> по <input id = 'finddateend' type='text' style='width:70px;' class=''> <button onclick='startfind();'>Найти</button></p>";
echo "<p>Номер отдела: <input type='text' style='width:25px;' class='findotd'> ";
echo " Фамилия исполнителя: <input type='text' class='findfio' ></p>";
echo " Документы: <input type='radio' name='findgotovnost' value = 'none' checked>Все";
echo " <input type='radio' name='findgotovnost' value = '100'>выпущенные";
echo " <input type='radio' name='findgotovnost' value = 'not100'>невыпущенные";
echo "</p>";
echo "</div>";
echo "<br>";
?>
<table class = 'autotable razdel'><tr><td class = 'table_header' colspan = 999 align = center>Переписка</td></tr></table>
<div id = 'poiskstage' class = '' style = 'display:none;'>
	<div id="findlayer" style="margin-top: 15px;">
		<input id = 'findcontentmail' type='text'  style='width:60%;' title = 'Введите дату, номер или содержание письма'><button onclick='startfind_mail();'> Найти</button>
		<script>
			let img = document.createElement("img");
			img.src = `include/question.png`;
			let div = document.createElement("div");
			div.classList.add("mailQuestion");
			div.style.marginLeft = 5 + 'px';
			div.dataset.title = "Поиск по диапазону дат:\n- указать диапазон между знаками # #;\n- после можно указать поисковое слово.\n\nНапример:\n#10.01.2022-16.03.2022#\nБудут выведены все строки между указанными \nдатами включая 10 и 16 число.\n\n#10.01.2022-16.03.2022# изделие\nВ указанном диапазоне будет задан поиск \nпо слову 'изделие'.";
			div.appendChild(img);
			findlayer.appendChild(div);
		</script>
	</div>
</div>
<br>

<table class = 'autotable razdel'><tr><td class = 'table_header' colspan = 999 align = center>Извещения</td></tr></table>
<div class = 'poiskstage' style = 'display:none;'>
	<br>
	<input id = 'izvesheniefind' type='text'  style='width:60%;' title = ''><button onclick='startfind_izveshenie();'> Найти</button>
</div>
<br>

<?
echo "<table class = 'autotable razdel'><tr><td class = 'table_header' colspan = 999 align = center>Этапы договора</td></tr></table>";
echo "<div id = 'poiskstage' class = '' style = 'display:none;'>";
echo "<p>Дата окончания работ с <input id = 'finddatedog1' type='text'  style='width:70px;' class='datefield'> по <input id = 'finddatedog2' type='text' style='width:70px;' class='datefield'> <button onclick='startfind_stage();'>Найти</button></p>";
echo "</div>";
echo "<br>";

echo "<table class = 'autotable razdel'><tr><td class = 'table_header' colspan = 999 align = center>Договорные документы</td></tr></table>";
echo "<div id = 'poiskdogovor' class = '' style = 'display:none;'>";
echo "<br>";
echo " <input type='radio' name='chkfinddog' value = '1'>договор заключен";
echo " <input type='radio' name='chkfinddog' value = '0'>договор не заключен <button onclick='startfind_dogovor();'>Найти</button>";
echo "</div>";
echo "<br>";

if ($_GET['print'] == 1) echo "</form>";
echo "</div>";
//echo "<p id='contenInput'></p>";

echo "<div id='find'>"; //Обновляемый блок

require ("dbconnect.php");
$dtable = new mgtable;

$dtable->htag = "find";

switch ($_POST['gotovnost']) {
	case 'none':
		$gotov_val = '';
	break;
	case '100':
		$gotov_val = " and gotovnost = '100' ";
	break;
	case 'not100':
		$gotov_val = " and gotovnost <> '100' ";
	break;
	default:
		$gotov_val = '';
	break;
}
$sql_date_field = " CONCAT(SUBSTRING_INDEX(date,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(date,'.',2),'.',-1), SUBSTRING_INDEX(date,'.',1)) ";
$sql_date_begin = " CONCAT(SUBSTRING_INDEX('" .$_POST['datebegin'] ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('" .$_POST['datebegin'] ."','.',2),'.',-1), SUBSTRING_INDEX('" .$_POST['datebegin'] ."','.',1)) ";
$sql_date_end = " CONCAT(SUBSTRING_INDEX('" .$_POST['dateend'] ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('" .$_POST['dateend'] ."','.',2),'.',-1), SUBSTRING_INDEX('" .$_POST['dateend'] ."','.',1)) ";

$_POST['datebegin'] == "" ? $find_date1 = "" : $find_date1 = "and " .$sql_date_field ." >= " .$sql_date_begin;
$_POST['dateend'] == "" ? $find_date2 = "" : $find_date2 = "and " .$sql_date_field ." <= " .$sql_date_end;

$dtable->filter = " and (naimenovenie LIKE '%" .$_POST['naimenovenie'] ."%' and otd LIKE '%" .$_POST['otd'] ."%' and fio LIKE '%" .$_POST['fio'] ."%' " .$gotov_val .$find_date1 .$find_date2 ."  ) ";

$dtable->dbtable = "docwork";
//$dtable->filter = " and doctype = 0 ";
$dtable->show_hide = 1;
$dtable->hfilter = " and hide <> 1 and doctype = 0 ";
$dtable->header_big_name = "Выборка";
$dtable->pos_nomer_col = 0;
$dtable->date_field_list = "date";
$dtable->sortirovka = "if(date = '' or date is null, 1, 0), SUBSTRING_INDEX(docwork.date,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(docwork.date,'.',2),'.',-1), SUBSTRING_INDEX(docwork.date,'.',1), id";
//$dtable->max_line = 30;
//if(isset($_GET['page'])) $dtable->show_page = $_GET['page'];
if($_POST['naimenovenie'] != "" or $_POST['otd'] != "" or $_POST['fio'] != "" or $_POST['datebegin'] != "" or $_POST['dateend'] != "") $dtable->datatable();


echo "</div>"; //find (Конец обновляемого блока)

echo "<div id='finddogovor'>"; //Обновляемый блок 2


require ("dbconnect.php");
$dtablest = new mgtablestage;

$dtablest->htag = "finddogovor";
$dtablest->dbtable = "stage";

$sql_date_fieldst= " CONCAT(SUBSTRING_INDEX(stage.datestop,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(stage.datestop,'.',2),'.',-1), SUBSTRING_INDEX(stage.datestop,'.',1)) ";
$sql_date_beginst = " CONCAT(SUBSTRING_INDEX('" .$_POST['datedog1'] ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('" .$_POST['datedog1'] ."','.',2),'.',-1), SUBSTRING_INDEX('" .$_POST['datedog1'] ."','.',1)) ";
$sql_date_endst = " CONCAT(SUBSTRING_INDEX('" .$_POST['datedog2'] ."','.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX('" .$_POST['datedog2'] ."','.',2),'.',-1), SUBSTRING_INDEX('" .$_POST['datedog2'] ."','.',1)) ";

$_POST['datedog1'] == "" ? $find_date1st = "" : $find_date1st = "and " .$sql_date_fieldst ." >= " .$sql_date_beginst;
$_POST['datedog2'] == "" ? $find_date2st = "" : $find_date2st = "and " .$sql_date_fieldst ." <= " .$sql_date_endst;

$dtablest->filter = "  " .$find_date1st .$find_date2st ."  ";
$dtablest->header_big_name = "Этапы договора";
$dtablest->date_field_list = "stage.datestop";

$dtablest->pos_nomer_col = 0;
$dtablest->sortirovka = "if(stage.datestop = '' or stage.datestop is null, 1, 0), SUBSTRING_INDEX(stage.datestop,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(stage.datestop,'.',2),'.',-1), SUBSTRING_INDEX(stage.datestop,'.',1), stage.id";
if($_POST['datedog1'] != "" or $_POST['datedog2'] != "") $dtablest->datatable();

echo "</div>"; //finddogovor (Конец обновляемого блока 2)

echo "<div id='finddog'>"; //Обновляемый блок 3

require ("dbconnect.php");
$dtabledog = new mgtabledog;

$dtabledog->htag = "finddogovor";
$dtabledog->dbtable = "dogovor";
$dtabledog->header_big_name = "Договорные документы";
$dtabledog->pos_nomer_col = 0;
$dtabledog->date_field_list = "date";
$dtabledog->filter = " and dogopen = '" .$_POST['dogopen'] ."' ";
if($_POST['dogopen'] != "") $dtabledog->datatable();

echo "</div>"; //finddogovor (Конец обновляемого блока 3)

echo "<div id='findmail'>"; //Обновляемый блок 4

echo "</div>"; //findmail (Конец обновляемого блока 4)

echo "<div id='findizveshenie'>"; //Обновляемый блок 5

echo "</div>"; //findizveshenie (Конец обновляемого блока 5)

?>
<script>

function chkzamok() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}
}

var radiofindgotovnost = 'none';
function startfind() {
	//$( "#find" ).empty();
	//$("#find").load("findworkdoc.php #find", "naimenovenie=" + $(".findnaimenovenie").val() + "&otd=" + $(".findotd").val() + "&fio=" + $(".findfio").val() + "&gotovnost=" + radiofindgotovnost,function(){chkzamok();});

$("#find").load('findworkdoc.php #find',{
		'naimenovenie':$(".findnaimenovenie").val(),
		'otd':$(".findotd").val(),
		'fio':$(".findfio").val(),
		'gotovnost':radiofindgotovnost,
		'datebegin':$("#finddatebegin").val(),
		'dateend':$("#finddateend").val()
	},
	function(){
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
		chkzamok();
	}
);

}

function startfind_stage() {
$("#find").load('findworkdoc.php #finddogovor',{
		'datedog1':$("#finddatedog1").val(),
		'datedog2':$("#finddatedog2").val()
	},
	function(){
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
		chkzamok();
	}
);

}


function startfind_mail() {
	/*$("#find").load('findworkdoc.php #findmail',{
			'contentmail':$("#findcontentmail").val()
		},
		function(){
			$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
			chkzamok();
		}
	);*/

	let sendObject = {
		"baseLayer": "findmail",
		"tabNumber": "-1",
		"page": 0,
		"fieldList": "datevh, nomervh, adresvh, contentvh, scanvh, countlistvh, sumnormchasvh, datereg, nomerreg, datecontrol, prim, dateish, nomerish, adresish, contentish, scanish, countlistish, sumnormchasish, fioispish",
		"find": findcontentmail.value
	};
	xhrLoad(sendObject);

}

function startfind_izveshenie() {
	let sendObject = {
		"baseLayer": "findizveshenie",
		"tab_id": "-1",
		"page": 0,
		"find": izvesheniefind.value
	};
	izvLoad(sendObject);
}


var var_chkfinddog;
function startfind_dogovor() {
$("#find").load('findworkdoc.php #finddog',{
		'dogopen':var_chkfinddog
	},
	function(){
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
		chkzamok();
	}
);

}

$(document).ready(function() {
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}

$(".findnaimenovenie").keyup(function(e) {
// $('#contenInput').text($(".findnaimenovenie").val());
if(e.keyCode==13){startfind();}
});

$(".findotd").keyup(function(e) {
if(e.keyCode==13){startfind();}
});

$(".findfio").keyup(function(e) {
if(e.keyCode==13){startfind();}
});

$("#finddatebegin").keyup(function(e) {
if(e.keyCode==13){startfind();}
});

$("#finddateend").keyup(function(e) {
if(e.keyCode==13){startfind();}
});

$("#findcontentmail").keyup(function(e) {
if(e.keyCode==13){startfind_mail();}
});

$("#izvesheniefind").keyup(function(e) {
if(e.keyCode==13){startfind_izveshenie();}
});


$("input[name='findgotovnost']").change(function() {
radiofindgotovnost = this.value;
startfind();
});

$("input[name='chkfinddog']").change(function() {
var_chkfinddog = this.value;
});


$("#finddatebegin, #finddateend, .datefield").mask("99.99.9999", {placeholder: "дд.мм.гггг" });

	$('.razdel').click(function () {
		$('.razdel').next('div').slideUp();
		$(this).next('div').slideDown();
		
		
		//$('.razdel').next('div').css({'display':'none'});
		//$(this).next('div').css({'display':'block'});
	});

});
</script>

