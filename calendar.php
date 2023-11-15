<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Рабочий календарь</title>
	<link href="jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet">
	<link href="css/indexstyle.css" rel="stylesheet">
	<link href="css/megatable.css" rel="stylesheet">
	<link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
	<style>

	</style>

    </head>
    <body>
<?//require ("localstyle.php");?>
<script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>
<script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
<script type="text/javascript" src="include/ajaxupload.3.5.js" ></script>
<script type="text/javascript" src="include/jquery.contextMenu.min.js"></script>
<link rel="stylesheet" type="text/css" href="include/jquery.contextMenu.min.css">
<script src="include/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="include/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="include/jquery.slimscroll.min.js"></script>



<style>
#monthbox {
	float: left;
	display: block;
	margin: 10px;
}

.all_month {
	font-family: "Trebuchet MS", Helvetica, sans-serif;
	font-size:13px;
	font-weight: bold;
	text-align: center;
}

.month {
	border-collapse: collapse;
	border: solid 1px black;
	font-family: "Trebuchet MS", Helvetica, sans-serif;
	font-size:13px;
	font-weight: bold;
}

.month td {
	border: solid 1px black;
	width: 27px;
	height: 27px;
	text-align: center;
}

.holyday {
	background:#EDDDDD;
}

.normalday {
	background:#CEEDCE;
}

.month .calendar_header {
	background: linear-gradient(to top, #55ABFF, #AAD8EB);
	height: 23px;
}

.month .shot_day_name {
	background: linear-gradient(to top, #AAD8EB, #AAD8EB);
	font-size:10px;
}

</style>

<script>

function calendar_day_rule(day,holydays) {   // функция для сохранения отредактированного текста с помощью ajax
$.ajax({
	url: 'function.php',				// php файл в который отправляется запрос
	type: 'POST',				//метод POST
	data: {
	relocate: 'calendar',
	day: day,     	//первый параметр запроса
	holydays:holydays		//второй параметр
	},				
	success:function (data) {      	//в случае удачи выполения запросов выполняется
	}
});
};

function refresh(god) {   // перезагрузка блока с указанным годом
$("#refblock").load('calendar.php',{
		'god':god
	},
	function(){

	}
);
};

</script>




<?
require ("dbconnect.php");

function cal_month($god, $monthnomer)
{
setlocale (LC_ALL, "Rus");
echo "<div id = 'monthbox'>";
$maxdays = cal_days_in_month(CAL_GREGORIAN, $monthnomer, $god); //всего дней в месяце
$nomerday = date("N", mktime(0,0,0,$monthnomer,1,$god)); //номер дня недели первого числа месяца
$nomerendday = date("N", mktime(0,0,0,$monthnomer,cal_days_in_month(CAL_GREGORIAN, $monthnomer, $god),$god)); //номер дня недели последнего числа месяца

$countday = 1;
echo "<table class = 'month'>";
echo "<tr>";
echo "<td class = 'calendar_header' colspan = 999 align = center>" .iconv('CP1251','UTF-8',strftime("%B", mktime(0,0,0,$monthnomer)))  ."</td>"; //малый заголовок
echo "<tr class = 'shot_day_name'><td>Пн</td><td>Вт</td><td>Ср</td><td>Чт</td><td>Пт</td><td>Сб</td><td>Вс</td></tr>";
for ($j = 0; $j < 6; $j++) 
{
	echo "<tr>";
	for ($i = 1; $i <= 7; $i++) 
	{
		if(($j == 0 and $i < $nomerday) or ($j == 5 and $i > $nomerendday)) echo "<td>";
		else if($countday <= $maxdays) 
		{	
			$date_for_db = str_pad($countday, 2, '0', STR_PAD_LEFT) ."." .str_pad($monthnomer, 2, '0', STR_PAD_LEFT) ."." .$god;
			$r = mysql_query("SELECT * FROM Calendar where date = '" .$date_for_db ."' ");
			$f = mysql_fetch_array($r);
			if($f['holydays'] == 1) echo "<td class = 'calendaritem holyday' data-day='" .$date_for_db ."'>" .$countday++;
			else if($f['holydays'] == 2) echo "<td class = 'calendaritem normalday' data-day='" .$date_for_db ."'>" .$countday++; 
				else 
				{
					if($i > 0 and $i < 6) echo "<td class = 'calendaritem normalday' data-day='" .$date_for_db ."'>" .$countday++;
					if($i == 6 or $i == 7) echo "<td class = 'calendaritem holyday' data-day='" .$date_for_db ."'>" .$countday++; 
				}
		}
		else echo "<td>";
		echo "</td>";
	}
	echo "</tr>";
}
echo "</table>";
echo "</div>";
}

echo "<div id = 'refblock'>";
if(isset($_POST['god'])) $god = $_POST['god'];
else $god = date("Y");
echo "<table class = 'all_month'>";
echo "<tr><td colspan = 999>";
echo "<a onclick=refresh('" .($god - 1) ."');><img src='include/resultset_previous.png'></a>";
echo " Рабочий календарь на " .$god ." год ";
echo "<a onclick=refresh('" .($god + 1) ."');><img src='include/resultset_next.png'></a>";
echo "</td></tr>";
echo "<tr><td>";
for($i=1;$i<=12;$i++) cal_month($god, $i);
echo "</td></tr>";
echo "</table>";

echo "<div id = 'summworkday'>";
if(isset($_POST['god'])) $god = $_POST['god'];
else $god = date("Y");
$maxdays = date('z', mktime(0, 0, 0, 12, 31, $god)) + 1; //всего дней в году
echo " Всего дней в " .$god ." году: " .$maxdays;
//echo " a " .date("z", mktime(0,0,0,13,33,$god)); //номер дня недели первого числа месяца
$workday = 0;
for($i=1;$i<=$maxdays;$i++)
{
	$r = mysql_query("SELECT * FROM Calendar where date = '" .date("d.m.Y", mktime(0, 0, 0, 1, $i, $god)) ."' ");
	$f = mysql_fetch_array($r);
	if($f['holydays'] == 2) {$workday++; continue;}
	if($f['holydays'] == 1) continue;
	if(date("N", mktime(0,0,0,1,$i,$god)) == 6 or date("N", mktime(0,0,0,1,$i,$god)) == 7) continue;
	$workday++;
}
echo "<br> Рабочих дней: " .$workday;
echo "</div>";
//echo "<br>";
//echo date("d.m.Y", mktime(0, 0, 0, 1, $i, $god)); //число по номеру дня
//echo " " .date("N", mktime(0,0,0,1,$i,$god)); //номер дня недели по номеру дня

function workdays($nmfst, $nmsnd)
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

function daycount($first, $second)
{
$dt1 = explode(".", $first);
$dt2 = explode(".", $second);
$nmfst = mktime(0, 0, 0, $dt1[1], $dt1[0], $dt1[2]);
$nmsnd = mktime(0, 0, 0, $dt2[1], $dt2[0], $dt2[2]);
return workdays($nmfst, $nmsnd);
}

echo "<br>Дни:" .daycount("28.12.2018", "03.01.2019");

?>

</div>

<script>
$(document).ready(function() {

	$('.calendaritem').click(function () {
		if($(this).hasClass('normalday')) {
			$(this).removeClass('normalday');
			$(this).addClass('holyday');
			calendar_day_rule($(this).data('day'),1);
			$('#summworkday').load('calendar.php #summworkday',{'god':<?echo $_POST['god']?$_POST['god']:$god?>});
			//$("#refblock").load('calendar.php',{'god':god});
			//alert(1);
		}
		else if($(this).hasClass('holyday')) {
			$(this).removeClass('holyday');
			$(this).addClass('normalday');
			calendar_day_rule($(this).data('day'),2);
			$('#summworkday').load('calendar.php #summworkday',{'god':<?echo $_POST['god']?$_POST['god']:$god?>});	
			//$("#refblock").load('calendar.php',{'god':god});
			//alert(2);
		}
	});

});

</script>

    </body>
</html>
