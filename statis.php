<?
session_start();

require ("dbconnect.php");

require ("megatable.php");

class mgtable extends megatable
{

public function table_big_header() //заголовок таблицы
{
	echo "<tr>";
	echo "<td class = 'table_big_header' colspan = 999 align = center>" .$this->header_big_name ."</td>"; //малый заголовок
	echo "</tr>";
	echo "<tr>";
	//echo "<td class = 'table_colspan_header' colspan = 2 align = center></td>";
	//echo "<td class = 'table_colspan_header' colspan = 6 align = center>Трудоёмкость подразделений, час</td>"; //малый заголовок
	//echo "<td class = 'table_colspan_header' colspan = 3 align = center>Расчетная суммарная трудоёмкость проекта, час</td>";
	echo "</tr>";
}

public function hide_field($r,$i)  //функция сокрытия полей в соответствии с заданными условиями hidefield в settings
{
	$r_settings = mysql_query("SELECT * FROM settings where tablename = 'statis1' and field = '" .mysql_field_name($r, $i) ."' ");
	$f_settings = mysql_fetch_array($r_settings);

	if ($f_settings['hidefield'] == "1") return true;
}

public function table_header($r) //заголовок таблицы
{
	for ($i=0; $i<mysql_num_fields($r); $i++)
	{
		$this->all_col($r,$f,$i,$j);
		if($this->hide_field($r,$i)) continue;

		$r_settings = mysql_query("SELECT * FROM settings where tablename = 'statis1' and field = '" .mysql_field_name($r, $i) ."'");
		$f_settings = mysql_fetch_array($r_settings);
		if($f_settings['showname']) echo "<td class = 'table_header' style = 'width:" .$f_settings['size'] ."'>" .$f_settings['showname'] ."</td>";
		else echo "<td class = 'table_header'>" .mysql_field_name($r, $i) ."</td>";
	}
}

public function bd_query_text()
{
	if($this->filename == "") $this->filename = basename($_SERVER['PHP_SELF']); // обновить файл из которого вызван
	if($this->show_hide == 0) $this->hfilter = " and hide <> 1 ";
	$dbquery = "SELECT " .$this->field ." FROM " .$this->dbtable ." where id >= 0 " .$this->hfilter .$this->filter ." ORDER BY " .$this->sortirovka;

//$dbquery = "select id, detid, nomer, datestart, datestop, 31trd, 31slg, 32trd, 32slg, 33trd, 33slg, 34trd, 34slg, 35trd, 35slg from stage where datestart != '' and datestop != '' and hide <> 1 and CONCAT(SUBSTRING_INDEX(datestart,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestart,'.',2),'.',-1), SUBSTRING_INDEX(datestart,'.',1)) <= '20190801' and CONCAT(SUBSTRING_INDEX(datestop,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestop,'.',2),'.',-1), SUBSTRING_INDEX(datestop,'.',1)) >= '20190801' order by SUBSTRING_INDEX(datestart,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestart,'.',2),'.',-1), SUBSTRING_INDEX(datestart,'.',1);";

	//$this->tabposcount = mysql_num_rows(mysql_query($dbquery));
	$this->pages_rule($dbquery);
	//$dbquery .= " LIMIT " .$this->show_line .", " .$this->max_line;
			//$fp = fopen("demotests.txt", "w");
			//fwrite($fp, $dbquery);
			//fclose($fp);
	return $dbquery;
}

public function workdays($nmfst, $nmsnd)
{
$workday = 0;
for($i=$nmfst;$i<=$nmsnd;$i+=(60*60*24))
{
	$r = mysql_query("SELECT * FROM Calendar where date = '" .date("d.m.Y", $i) ."' ");
	$f = mysql_fetch_array($r);
	if($f['holydays'] == 2) {$workday++; continue;}
	if($f['holydays'] == 1) continue;
	if(date("N", $i) == 6 or date("N", $i) == 7) continue;
	$workday++;
}
return $workday;
}

public function workperiod($first, $second)
{
if($first == 0 or $first == "" or $second == 0 or $second == "") return;
$dt1 = explode(".", $first);
$dt2 = explode(".", $second);
$sm = 12;
$nmfst = mktime($sm, 0, 0, $dt1[1], $dt1[0], $dt1[2]);
$nmsnd = mktime($sm, 0, 0, $dt2[1], $dt2[0], $dt2[2]);

$workday = 0;
for($i=0;$i<=999;$i++)
//while (mktime($sm, 0, 0, ($dt1[1] + $i), 0, $dt1[2]) <= $nmsnd)
{
$dt1 = explode(".", date("d.m.Y", $nmfst));

	if($i == 0) 
	{
		if($nmsnd > mktime($sm, 0, 0, ($dt1[1] + 1), 0, $dt1[2])) 
			$period .= $this->daycount(date("d.m.Y", $nmfst), date("d.m.Y", mktime($sm, 0, 0, ($dt1[1] + 1), 0, $dt1[2])))  ."|";
		
		if($nmsnd <= mktime($sm, 0, 0, ($dt1[1] + 1), 0, $dt1[2])) 
			{$period .= $this->daycount(date("d.m.Y", $nmfst), date("d.m.Y", $nmsnd)); break;}
	}
	if($i > 0) 
	{
		if(mktime($sm, 0, 0, ($dt1[1] + $i + 1), 0, $dt1[2]) < $nmsnd) 
			$period .= $this->daycount(date("d.m.Y", mktime($sm, 0, 0, ($dt1[1] + $i), 1, $dt1[2])), date("d.m.Y", mktime($sm, 0, 0, ($dt1[1] + $i + 1), 0, $dt1[2]))) ."|"; 
		if(mktime($sm, 0, 0, ($dt1[1] + $i), 0, $dt1[2]) >= $nmsnd) {$period .= $this->daycount(date("d.m.Y", mktime($sm, 0, 0, ($dt1[1] + $i -1 ), 1, $dt1[2])), date("d.m.Y", $nmsnd)); break;}
	}

}
return $period;
}

public function daycount($first, $second)
{
$dt1 = explode(".", $first);
$dt2 = explode(".", $second);
$nmfst = mktime(12, 0, 0, $dt1[1], $dt1[0], $dt1[2]);
$nmsnd = mktime(12, 0, 0, $dt2[1], $dt2[0], $dt2[2]);
return $this->workdays($nmfst, $nmsnd);
//return $first ."-" .$second;
}

public function monthcount($first, $second)
{
$dt1 = explode(".", $first);
$dt2 = explode(".", $second);
$nmfst = mktime(12, 0, 0, $dt1[1], 1, $dt1[2]);
$nmsnd = mktime(12, 0, 0, $dt2[1], 1, $dt2[2]);
$countmonth = 0;
for($i=0;$i=999;$i++)
{
	if(mktime(12, 0, 0, ($dt1[1] + $countmonth++), 1, $dt1[2]) == $nmsnd) break;
}
return $countmonth - 1;
}

public $summ_31 = 0;
public function add_col($r,$f,$i,$j)
{
	$f['dateprobablestop'] == "" ? $datestop = $f['datestop'] : $datestop = $f['dateprobablestop'];
	$datestart = $f['datestart'];
	if(!isset($j) and $i == 6) print "<td class = 'table_header" .$this->nomer_col_style ."' style = 'width:25px;'>Всего дней</td>";  //заголовок
	if(isset($j) and $i == 6) print "<td class = 'simplefield1' >" .$this->daycount($datestart, $datestop) ."</td>";  //колонка

	if($f['31slg'] == 0 or $f['31slg'] =='') $f['31slg'] = 1;
	if(!isset($j) and $i == 6) print "<td class = 'table_header" .$this->nomer_col_style ."' style = 'width:25px;'>Трудоемкость в день</td>";  //заголовок
	if(isset($j) and $i == 6) {print "<td class = 'simplefield1' >" .($f['31trd'] / $this->daycount($datestart, $datestop))*$f['31slg'] ."</td>";  //колонка
	//$this->summ_31 += ($f['31trd'] / $this->daycount($datestart, $datestop))*$f['31slg'];
	}
	if(!isset($j) and $i == 6) print "<td class = 'table_header" .$this->nomer_col_style ."' style = 'width:25px;'>Трудоемкость в час</td>";  //заголовок
	if(isset($j) and $i == 6) print "<td class = 'simplefield1' >" .($f['31trd'] / ($this->daycount($datestart, $datestop)*8))*$f['31slg'] ."</td>";  //колонка
	if(!isset($j) and $i == 6) print "<td class = 'table_header" .$this->nomer_col_style ."' style = 'width:25px;'>Дни</td>";  //заголовок
	if(isset($j) and $i == 6) print "<td class = 'simplefield1' >" .$this->daycount('1.3.2018', '0.4.2018') ."</td>";  //колонка
}

}


$dtable = new mgtable;

echo "<script>$('varframe').empty();</script>";
$dtable->htag = "varframe";
$dtable->field = "id, detid, nomer, datestart, datestop, dateprobablestop, 31trd, 31slg, 32trd, 32slg, 33trd, 33slg, 34trd, 34slg, 35trd, 35slg";
$dtable->dbtable = "stage";
//$dtable->filter = " and datestart != '' and datestop != '' and CONCAT(SUBSTRING_INDEX(datestart,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestart,'.',2),'.',-1), SUBSTRING_INDEX(datestart,'.',1)) <= '20190801' and CONCAT(SUBSTRING_INDEX(datestop,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestop,'.',2),'.',-1), SUBSTRING_INDEX(datestop,'.',1)) >= '20190801'";
$dtable->filter = " and datestart != '' and datestop != '' ";
$dtable->sortirovka = "SUBSTRING_INDEX(datestart,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestart,'.',2),'.',-1), SUBSTRING_INDEX(datestart,'.',1);";
$dtable->getdata = $_SERVER['QUERY_STRING'];
//$dtable->filter = " and detid = " .$_GET['id'];

$dtable->header_big_name = "Статистика проекта";
$dtable->pos_nomer_col = 0;
//$dtable->datatable();
//echo "31 отд: " .$dtable->summ_31;
//echo "<br><br><br><br>";

function monthzagr($god, $otdlist, $trdlist, $slglist, $show)
{
$dtable = new mgtable;
setlocale (LC_ALL, "Rus");
if($show == 1) echo "<table id = 'pplcount' class = 'autotable' style = 'width:1010px;text-align:center;margin:auto;'>";
if($show == 1) echo "<tr><td class = 'table_big_header' colspan = 999 align = center>Свободные ресурсы отделов</td></tr>";
echo "<tr>";
echo "<td class = 'table_colspan_header' colspan = 999 align = center>" .$god ." год</td>";
echo "</tr>";
echo "<tr>";
echo "<td class = 'table_header' style = 'width:50px;'>Отдел</td>";
for($i=1;$i<=12;$i++) echo "<td class = 'table_header' >" .iconv('CP1251','UTF-8',strftime("%B", mktime(0,0,0,$i))) ."</td>";
echo "</tr>";
for($j=0;$j<count($otdlist);$j++) 
{
echo "<tr>";
echo "<td class = 'table_header'>" .$otdlist[$j] ."</td>";



	for($i=1;$i<=12;$i++) 
	{			
		$r1 = mysql_query("SELECT * FROM pplcount where otd = '" .$otdlist[$j] ."' and month = '" .$i ."' and year = '" .$god ."' ");
		$pplcnt = mysql_fetch_array($r1);
		if ($pplcnt['count'] == 0 or $pplcnt['count'] == "") $pplcnt['count'] = 20;

		$dt1 = "01." .str_pad($i, 2, '0', STR_PAD_LEFT) ."." .$god;
		$dt2 = "00." .str_pad(($i+1), 2, '0', STR_PAD_LEFT) ."." .$god;
		$maxmonthday = $dtable->daycount($dt1, $dt2); //число рабочих дней в расчетном месяце
		$resurs = $pplcnt['count'] * $maxmonthday * 8;

		$dtstart = "CONCAT(SUBSTRING_INDEX(datestart,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestart,'.',2),'.',-1), SUBSTRING_INDEX(datestart,'.',1))";
		$dtstop = "CONCAT(SUBSTRING_INDEX(datestop,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(datestop,'.',2),'.',-1), SUBSTRING_INDEX(datestop,'.',1))";
		$dtprostop = "CONCAT(SUBSTRING_INDEX(dateprobablestop,'.',-1), SUBSTRING_INDEX(SUBSTRING_INDEX(dateprobablestop,'.',2),'.',-1), SUBSTRING_INDEX(dateprobablestop,'.',1))";
		$date1 = $god .str_pad($i, 2, '0', STR_PAD_LEFT) ."01";
		$date2 = $god .str_pad(($i+1), 2, '0', STR_PAD_LEFT) ."00";
		$r = mysql_query("SELECT * FROM stage where hide != 1 and datestart != '' and datestop != '' and ((" .$dtstart ." <= '" .$date1 ."' or " .$dtstart ." <= '" .$date2 ."' ) and (" .$dtstop ." >= '" .$date2 ."' or ( " .$dtstop ." <= '" .$date2 ."' and " .$dtstop ." >= '" .$date1 ."' ))) order by " .$dtstart ." ");
		$ctrl = $_SESSION['alldata'];
		$trudoemkost = "";
		$control = "";
		for($ii = 0; $ii < mysql_num_rows($r);$ii++)
		{
			$f = mysql_fetch_array($r);
			$wday = explode("|", $f['period']);
			$summ_wday = 0;
			for($iii=0;$iii<count($wday);$iii++) $summ_wday += $wday[$iii];

			if ($f[$trdlist[$j]] == 0 or $f[$trdlist[$j]] == "") $kslg = 1;
			else $kslg = str_replace(",", ".", $f[$slglist[$j]]);

			if($f[$trdlist[$j]] != 0 and $f[$trdlist[$j]] != "" and $summ_wday != 0) 
			{
				//$f[$trdlist[$j]] - общая трудоемкость работы
				//$wday[$dtable->monthcount($f['datestart'], $dt1)] - число рабочих дней проекта в расчетном месяце
				//$dtable->monthcount($f['datestart'], $dt1) - номер месяца от начала проекта
				//$summ_wday - общее число рабочих дней работы
				//$kslg - коэффициент сложности
				//$pplcnt['count'] - количество людей в отделе в расчетом месяце
				//$resurs - ресурс отдела по нормачасам
				if($ctrl == '1') 
				{
					$k = $dtable->monthcount($f['datestart'], $dt1);
					$control .= "трд:" .$f[$trdlist[$j]] ."(сл:" .$kslg .") дней:" .$summ_wday ."(" .($k+1) ."м" .$wday[$k] ."д) людей:" .$pplcnt['count'] ."(" .$resurs .")<br>";
				}

				@$trudoemkost += (($f[$trdlist[$j]]/$summ_wday) * $wday[$dtable->monthcount($f['datestart'], $dt1)]) * $kslg;
			}
		}
		@$zagruzka = ($resurs - $trudoemkost) / $resurs;
		if($ctrl == '1') echo "<td class = 'simplefield' style = 'width:80px;'>" .$control ."<br>" .round($trudoemkost, 2) ."(" .round($zagruzka * 100, 2) ." %)</td>"; 
		else {echo "<td class = 'simplefield' style = 'width:80px;'>" .round($zagruzka * 100, 2) ." %</td>"; 
			//if ($god == date("Y")-1) $_SESSION['grafic_data'][$j][$i-1] = round($zagruzka * 100, 2);
			if ($god == date("Y")) $_SESSION['grafic_data'][$j][$i-1] = round($zagruzka * 100, 2);
			//if ($god == date("Y")+1) $_SESSION['grafic_data'][$j][$i-1+24] = round($zagruzka * 100, 2); 
			}
	}
echo "</tr>";
}
if ($show == 2) echo "</table>";
// if ($god == date("Y")) $_SESSION['grafic_data'] = $mass1;
}


function countppl($god, $otdlist, $show)
{
setlocale (LC_ALL, "Rus");
//echo "<br>";
if($show == 1) echo "<table id = 'pplcount' class = 'autotable' style = 'width:1010px;text-align:center;margin:auto;'>";
if($show == 1) echo "<tr><td class = 'table_big_header' colspan = 999 align = center>Фактическая численность сотрудников в отделах</td></tr>";
echo "<tr>";
echo "<td class = 'table_colspan_header' colspan = 999 align = center>" .$god ." год</td>";
echo "</tr>";
echo "<tr>";
echo "<td class = 'table_header' style = 'width:50px;'>Отдел</td>";
for($i=1;$i<=12;$i++) echo "<td class = 'table_header' >" .iconv('CP1251','UTF-8',strftime("%B", mktime(0,0,0,$i))) ."</td>";
echo "</tr>";
for($j=0;$j<count($otdlist);$j++) 
{
echo "<tr>";
echo "<td class = 'table_header'>" .$otdlist[$j] ."</td>";
	for($i=1;$i<=12;$i++) 
	{
	$r = mysql_query("SELECT * FROM pplcount where otd = '" .$otdlist[$j] ."' and month = '" .$i ."' and year = '" .$god ."' ");
	$f = mysql_fetch_array($r);
	echo "<td id = 'nid_month_" .$i ."_year_" .$god ."_otd_" .$otdlist[$j] ."_count_pplcount' class = 'simplefield' style = 'width:80px;'>" .$f['count'] ."</td>";
	
	}
echo "</tr>";
}
if ($show == 2) echo "</table>";
}

//Расчет периода при изменении или вводе новой строки
$dtable = new mgtable;
setlocale (LC_ALL, "Rus");

$rc = mysql_query("SELECT * FROM Calendar where upd = '1' ");
if(mysql_num_rows($rc) > 0) $r = mysql_query("SELECT * FROM stage where hide != '1' ");
else $r = mysql_query("SELECT * FROM stage where hide != '1' and (period = '' or upd = '1') ");
for($i=0;$i<mysql_num_rows($r);$i++)
{
	$f = mysql_fetch_array($r);
	$date1 = $f['datestart'];
	$date2 = ($f['dateprobablestop'] != "" ? $f['dateprobablestop'] : $f['datestop']);
	if($f['datestart'] == "" or $f['datestop'] == "") mysql_query("UPDATE stage SET period = 0 WHERE id = '" .$f['id'] ."' ");
	else mysql_query("UPDATE stage SET period = '" .$dtable->workperiod($date1, $date2) ."' WHERE id = '" .$f['id'] ."' ");
	mysql_query("UPDATE stage SET upd = '0' WHERE id = '" .$f['id'] ."' ");

	if(mysql_num_rows($rc) > 0 and $i == (mysql_num_rows($r)-1)) 
	{
		for($ic=0;$ic<mysql_num_rows($rc);$ic++) 
		{
			$fc = mysql_fetch_array($rc);
			mysql_query("UPDATE Calendar SET upd = '0' WHERE id = '" .$fc['id'] ."' ");
		}
	}
}

if($_SESSION['alldata'] == '1') echo "<input type='checkbox' name='chkstatfull' class = 'checkfield' checked> Показать данные";
else echo "<input type='checkbox' name='chkstatfull' class = 'checkfield' > Показать данные";

$otdlist = array("31", "32", "33", "34", "35");
$trdlist = array("31trd", "32trd", "33trd", "34trd", "35trd");
$slglist = array("31slg", "32slg", "33slg", "34slg", "35slg");
monthzagr(date("Y")-1, $otdlist, $trdlist, $slglist, 1);
monthzagr(date("Y"), $otdlist, $trdlist, $slglist, 0);
monthzagr(date("Y")+1, $otdlist, $trdlist, $slglist, 2);
echo "<br>";

$_SESSION['grafic_x'] = 1000;

//echo "<script>$.post('grafic.php',{grafic_data:'" .$_SESSION['grafic_data'] ."', data2:'1'});</script>";
echo "<table style = 'margin:auto;text-align:center;'><tr><td>";
echo "<div id = 'grafic'>";
echo "<img src = ''>";
echo "</div>";
echo "</td></tr></table>";

//unset($_SESSION['grafic_x']);
//unset($_SESSION['grafic_data']);
//echo $_SESSION['grafic_x'];
echo "<br>";
//print_r($_SESSION['grafic_data']);

echo "<br>";
$otdlist = array("31", "32", "33", "34", "35");
$god = date("Y");
countppl($god-1, $otdlist, 1);
countppl($god, $otdlist, 0);
countppl($god+1, $otdlist, 2);

?>
<script>
if (zamok == 1) {open_edit();}
if (zamok == 0) {close_edit();}

$('[name="chkstatfull"]').change(function() {
//console.log("upd_box: " + $(this).data('id') + " chk: " + $(this).prop('checked'));
if ($(this).prop('checked') == true) {$.post('function.php',{relocate:'session', data1:'alldata', data2:'1'});refresh('statis.php', 'varframe', '');}
if ($(this).prop('checked') == false) {$.post('function.php',{relocate:'session', data1:'alldata', data2:'0'});refresh('statis.php', 'varframe', '');}
});

var c = Math.random();
$("#grafic img").attr('src', 'grafic.php?p=' + c);

</script>