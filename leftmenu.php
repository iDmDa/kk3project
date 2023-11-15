<div id = "leftmenubox">

<div class = 'menuitem_header'><input type='text' style='width:90%;' class='findmenuizd' placeholder='Индекс или договор:' title = 'Введите индекс изделия или номер договора'></div>

<div>
<input type='radio' name='leftmenu_tree_radio' value = 'leftmenu_simple_tree' checked>Дерево
<input type='radio' name='leftmenu_tree_radio' value = 'leftmenu_catalog_tree'>Каталог
</div>

<div id = "simple_tree" style = "display:block;">

<div id = "menuframe" class = 'menuscroll'>
<?
require ("dbconnect.php");
if($_GET["izdname"] == "") $r = mysql_query("SELECT * FROM izdelie where hide <> 1 and notactive = 0 order by  if(name = '', 1, 0), sort, name, id");
//else $r = mysql_query("SELECT * FROM izdelie where hide <> 1 and notactive = 0 and (name LIKE '%" .$_GET['izdname'] ."%') order by  if(name = '', 1, 0), sort, name, id");
else $r = mysql_query("select izdelie.*, dogovor.nomer from izdelie inner join dogovor on izdelie.id = dogovor.detid where izdelie.hide <> 1 and izdelie.notactive = 0 and (izdelie.name LIKE '%" .$_GET['izdname'] ."%' or dogovor.nomer LIKE '%" .$_GET['izdname'] ."%') order by  if(izdelie.name = '', 1, 0), izdelie.sort, izdelie.name, izdelie.id");

echo "<table id = 'leftmenu'>";
echo "<tr><td class =''></td></tr>";

for($i=0;$i<mysql_num_rows($r);$i++)
{
$f = mysql_fetch_array($r);
echo "<tr><td class ='menufielditem'>";
echo "<div id = '" .$f['id'] ."_name_izdelie' class = 'menuitem' onClick=menuchoice('" .$f['id'] ."','0');>";
echo $f['name'];
echo "</div>";
echo "</td></tr>";
}
echo "</table>";

echo "<div class = 'button_field button_layer' style = 'display:none;float:left;'>";
echo "<a onClick = okno_settings('dialog','&htag=dialog&dbtable=izdelie');><img src = 'include/new window.png' title = ''></a>";
echo "</div>";

?>
<p><br></p>
</div>

<div id = "varframe">

</div>
</div>

<div id = "catalog_tree">
kjgioerguioe
</div>

</div>