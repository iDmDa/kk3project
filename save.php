<?php
	include("dbconnect.php");
                      $id= filter_input (INPUT_POST ,  'id' , FILTER_SANITIZE_STRING );
                      $id=explode('_', $id);    //$id массив с данными через "_", получаем построчный массив $id[x]
                      $item_id=mysql_real_escape_string($id[0]); //изменяет данные для записи в базу данных
/*
$fp = fopen("file.txt", "w");
fwrite($fp, $item_id);
fclose($fp);
*/
	if($item_id != "nid") 
	{
			echo "<script>console.log('123');</script>";
                      $item_pole=mysql_real_escape_string($id[1]);
                      $item_table=mysql_real_escape_string($id[2]);
                      $content = $_POST['content']; //get posted data
	$content = mysql_real_escape_string($content);	//escape string	
	$content1 = strip_tags($content, "<br><sup><small>");
	$content1 = rtrim($content1, "<br>");
	mysql_query("UPDATE $item_table SET $item_pole = '$content1' WHERE id = '$item_id' ");
	if($item_table == "stage" or $item_table == "docwork" or $item_table == "mailbox") mysql_query("UPDATE $item_table SET upd = '1' WHERE id = '$item_id' ");

	if ($id[1] == "data") //доп ячейка, перерасчет даты формата дд.мм.гггг в ггггммдд для сортировки по дате
	{
	$srd=explode(".", $content1);
	$srd[0] = preg_replace("/[^,.0-9]/", '', $srd[0]); //обрезать весь текст и оставить только цифры
	$srd[1] = preg_replace("/[^,.0-9]/", '', $srd[1]);
	$srd[2] = preg_replace("/[^,.0-9]/", '', $srd[2]);
	if (strlen($srd[0]) == 1) $srd[0] = "0" .$srd[0]; //проверить количество символов в блоках даты
	if (strlen($srd[1]) == 1) $srd[1] = "0" .$srd[1];
	if (strlen($srd[2]) == 2) $srd[2] = "20" .$srd[2];

	if (strlen($srd[0]) == 2 and strlen($srd[1]) == 2 and strlen($srd[2]) == 4) mysql_query("UPDATE $item_table SET sortdata = '" .$srd[2].$srd[1].$srd[0] ."' WHERE id = '$item_id' ");
	}
//print $itemid;
	if ($content) 
                     {
                       print $content;
                     }
                     else print 'в„–1';
	}

	if($item_id == "nid") 
	{
		$item_fld1=mysql_real_escape_string($id[0]); //изменяет данные для записи в базу данных
		$item_fld2=mysql_real_escape_string($id[1]);
		$item_fld3=mysql_real_escape_string($id[2]);
		$item_fld4=mysql_real_escape_string($id[3]);
		$item_fld5=mysql_real_escape_string($id[4]);
		$item_fld6=mysql_real_escape_string($id[5]);
		$item_fld7=mysql_real_escape_string($id[6]);
		$item_pole=mysql_real_escape_string($id[7]);
		$item_table=mysql_real_escape_string($id[8]);
		$content = $_POST['content']; //get posted data
		$content = mysql_real_escape_string($content);	//escape string	
		$content1 = strip_tags($content, "<br><sup><small>");
		$content1 = rtrim($content1, "<br>");
			//$fp = fopen("demotests.txt", "w");
			//fwrite($fp, "UPDATE " .$item_table ." SET " .$item_pole ." = '" .$content1 ."' WHERE " .$item_fld2 ." = '" .$item_fld3 ."' and " .$item_fld4 ." = '" .$item_fld5 ."' and " .$item_fld6 ." = '" .$item_fld7 ."' ");
			//fclose($fp);

		$r = mysql_query("SELECT * FROM pplcount where " .$item_fld2 ." = '" .$item_fld3 ."' and " .$item_fld4 ." = '" .$item_fld5 ."' and " .$item_fld6 ." = '" .$item_fld7 ."' ");
		$f = mysql_fetch_array($r);
		if($f['id'] != "") mysql_query("UPDATE " .$item_table ." SET " .$item_pole ." = '" .$content1 ."' WHERE " .$item_fld2 ." = '" .$item_fld3 ."' and " .$item_fld4 ." = '" .$item_fld5 ."' and " .$item_fld6 ." = '" .$item_fld7 ."' ");
		else mysql_query ("INSERT INTO " .$item_table ." (" .$item_fld2 .", " .$item_fld4 .", " .$item_fld6 .", " .$item_pole .") VALUES ('" .$item_fld3 ."', '" .$item_fld5 ."', '" .$item_fld7 ."', '" .$content1 ."')");
	}
  ?>