<?
{
	include("dbdata.php");
	$link = mysqli_connect($db_host, $db_login, $db_pass, $db_name);
	
	if(!function_exists('mysql_query')) {
		function mysql_query($sql)
		{
			global $link;
			return mysqli_query($link, $sql);
		}
	
		function mysql_num_rows($result)
		{
			return mysqli_num_rows($result);
		}
		
		function mysql_fetch_array($result)
		{
			return mysqli_fetch_array($result);
		}
		
		function mysql_num_fields($result)
		{
			return mysqli_num_fields($result);
		}
		
		function mysql_field_name($result, $i)
		{
			return mysqli_fetch_field_direct($result, $i)->name;
		}
		
		function mysql_real_escape_string($n)
		{
			global $link;
			return mysqli_real_escape_string($link, $n);
		}
	}
}

/*
mysql_connect("localhost", "root", "");
mysql_select_db($db_name);
*/
?>