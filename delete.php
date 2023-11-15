<?
require ("dbconnect.php");
require ("global_path.php");  //$save_dir = "/uploads/";


$id = $_GET['id'];
$table = $_GET['table'];


	$r=mysql_query("SELECT * FROM " .$table ." where id = " .$id);
	$f=mysql_fetch_array($r);

if (isset($f['prefix']))
{
	$fp = iconv("UTF-8","CP1251",".." .$save_dir .$f['local_path'] ."/" .$f['prefix'] ."_" .$f['filename']);
	$del_path = ".." .$delete_dir ."/";
	$newfp = iconv("UTF-8","CP1251", $del_path .$f['prefix'] ."_" .$f['filename']);
	if(!is_dir($del_path)) mkdir($del_path, 0700, $recursive = true); 

	if (copy($fp, $newfp)) unlink($fp);
}

//mysql_query ("DELETE FROM " .$table ." WHERE id=" .$id);
mysql_query ("UPDATE " .$table ." SET hide=1 WHERE id=" .$id);
//echo "Запись удалена";
 
?>