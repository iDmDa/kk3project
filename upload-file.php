<?
require ("dbconnect.php");

require ("global_path.php");  //переменная $save_dir (по умолчанию = "/uploads/")


$uploaddir = ".." . iconv('UTF-8','CP1251', $save_dir. $_GET['tabname']) ."/"; 

if(!is_dir($uploaddir)) mkdir($uploaddir, 0700, $recursive = true); 

$img = $_FILES['uploadfile'];  

if(!empty($img)) 
{

	if($img['name']!=='') $fileprefix = $_SERVER['REQUEST_TIME']; //префикс с временем добавления файла

	$nnm = iconv('UTF-8','CP1251',$fileprefix. "_" .$img['name']);
	move_uploaded_file($img['tmp_name'], $uploaddir.$nnm);
		
	mysql_query ("INSERT INTO uplfiles (tabname, type, detid, prefix, filename, maskname, prim, local_path) VALUES ('" .$_GET['tabname'] ."','" .$_GET['type'] ."','" .$_GET['detid'] ."','".$fileprefix ."', '" .$img['name'] ."', '" .$img['name'] ."', '', '" .$_GET['tabname'] ."')");


}
?>