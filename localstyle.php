
<?

function getInfoBrowser(){
$agent = $_SERVER['HTTP_USER_AGENT'];
preg_match("/(MSIE|Opera|Firefox|Chrome|Safari|Chromium|Version)(?:\/| )([0-9.]+)/", $agent, $bInfo);
$browserInfo = array();
$browserInfo['name'] = ($bInfo[1]=="Version") ? "Safari" : $bInfo[1];
$browserInfo['version'] = $bInfo[2];     
return $browserInfo;}

//print_r (getInfoBrowser());
//echo getInfoBrowser()['version'];

//if (getInfoBrowser()['name'] == 'Firefox' and getInfoBrowser()['version'] < 60) echo "браузер устарел";

if (getInfoBrowser()['name'] == 'Firefox' and getInfoBrowser()['version'] >= 60) 
{
?>
<style>
.table_header_block {
	position: sticky;
	top: 0px;
	/*border: solid 2px black;*/
	background: black;
	border-top:2px solid #AAD8EB; /* защита от съедания верхней полосой */
}
</style>
<?
}
?>
