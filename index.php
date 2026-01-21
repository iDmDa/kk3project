<?
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Проекты КК-3</title>
		<link href="jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet">
		<link href="css/main.css?v=<?=time();?>" rel="stylesheet">
		<link href="css/megatable.css?v=<?=time();?>" rel="stylesheet">
		<link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
		<link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
		<link rel="manifest" href="favicon_io/site.webmanifest">
		<style>

		</style>

    </head>
    <body>
<?
//require ("localstyle.php");
require ("dbconnect.php");
?>
<script src = "js/mailbox.js?v=<?=time();?>"></script>
<script src = "js/izv.js?v=<?=time();?>"></script>
<script src = "js/docwork.js?v=<?=time();?>"></script>
<script src = "js/common.js?v=<?=time();?>"></script>
<script src="jquery-ui-1.12.1.custom/external/jquery/jquery.js"></script>
<script src="jquery-ui-1.12.1.custom/jquery-ui.js"></script>
<script type="text/javascript" src="include/ajaxupload.3.5.js" ></script>
<script type="text/javascript" src="include/jquery.contextMenu.min.js"></script>
<link rel="stylesheet" type="text/css" href="include/jquery.contextMenu.min.css">
<script src="include/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="include/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="include/jquery.slimscroll.min.js"></script>

<script>
window.openStatus = "fff";
var activefile = "none"; //переменная для определения текущей страницы для обновления страницы
var contentold={};   //объявляем переменную для хранения неизменного текста
var contentid = 0;
function savedata(elementidsave,contentsave) {   // функция для сохранения отредактированного текста с помощью ajax
	$.ajax({
		url: 'save.php',				// php файл в который отправляется запрос
		type: 'POST',				//метод POST
		data: {
			content: contentsave,     	//первый параметр запроса
			id:elementidsave		//второй параметр
		},				
		success:function (data) {      	//в случае удачи выполения запросов выполняется
			if (data == contentsave)   //сервер прислал нам отредактированый текст, значит всё ок
			{                                                                      
				$('#'+elementidsave).html(data);        //записываем присланные данные от сервера в элемент, который редактировался
			}
		}
	});
};

function update_db(id_pole_table,contentsave) {   // функция для сохранения отредактированного текста с помощью ajax
	$.ajax({
		url: 'save.php',				// php файл в который отправляется запрос
		type: 'POST',				//метод POST
		data: {
			content: contentsave,     	//первый параметр запроса
			id:id_pole_table		//второй параметр
		},				
		success:function (data) {      	//в случае удачи выполения запросов выполняется

		}
	});
};

function findsave(){					//сохраняет найденный в фокусе текст
	$('.autotable [contenteditable="true"]').unbind();  //уничтожить ранее вызванный элемент
	$('.autotable [contenteditable="true"]')                 // поиск всех элементов где contenteditable="true"
	.mousedown(function (e)                       //  обрабатываем событие нажатие мышки 
	{
		if(contentid != this.id) //блокировать повторное переназначение исходного текста
		{
			contentid = this.id;
			contentold[this.id]=$(this).html();        // текст до редактирования
			console.log("(findsave) " + this.id + " " + $(this).html());	    //вывести в консоль браузера значение текущего тега
		}
	})
	.bind('keydown', function(e) //фильтр ввода
	{        
		if(e.keyCode==27) // обработчик нажатия Escape
		{ 
			$(this).html(contentold[this.id]);	// возвращаем текст до редактирования	
		}
		if (e.keyCode == 6666613) 
		{
			// insert 2 br tags (if only one br tag is inserted the cursor won't go to the second line)
			document.execCommand('insertHTML', false, '<br>');
			// prevent the default behaviour of return key pressed
			return false;
		}
		if (e.keyCode == 13) //замена <div> на <br> при вводе enter
		{
			document.execCommand('insertText', false, '\n');
			return false;
		}

	})

	.blur(function (event)                   //  обрабатываем событие потери фокуса
	{
		var  contentsave = $(this).html();           // текст для сохраненния
		if (contentsave!=contentold[this.id])  // если текст изменился           
		{    
			savedata(this.id,contentsave);   // отправляем на сервер
			console.log("(save) " + contentsave);	
		}
	});
};

function refresh(loadfile, htag, getdata) {
	$.ajax({

	success: function(data)
		{
			if(loadfile == "mailbox.php") document.getElementById("varframe").innerHTML = "";
			$('#' + htag).prepend('<div id = "loader_layer"><div class = "layer_center"><img src = "include/loader_circle.gif"></div></div>');
			console.log("(refresh) htag: " + htag + "; getdata: " + getdata + "; loadfile: " + loadfile);
			$('#' + htag).load(loadfile + "?&htag=" + htag + getdata);
		}
	
	});

}; 


function add_refresh(table, field, fieldvalue, loadfile, htag, getdata) {
	console.log("(add_refresh)");
	$.ajax({
	url: 'add.php?table=' + table + "&field=" + field + "&value=" + fieldvalue, 
	success: function(data)
		{
			refresh(loadfile, htag, getdata);
		}
	});
}; 

var pass= 1;
function delete_refresh(table, id, loadfile, htag, getdata) {
	x=prompt('Введите пароль для удаления:', 'Введите: 1');
	if (x==pass) 
		delete_refresh_pass(table, id, loadfile, htag, getdata);
	else  
		alert('Вы ввели неправильный пароль.');
}

function delete_refresh_pass(table, id, loadfile, htag, getdata) {
	$.ajax({
	url: 'delete.php?table=' + table + "&id=" + id,
	success: function(data)
		{
			refresh(loadfile, htag, getdata);
			loadsection(izdelieid, sectionid);
			//refresh(loadfile, htag, getdata);
		}
	
	});
}; 

var izdelieid=0;
var sectionid=0;
function menuchoice(izdelie, section) {
	if (izdelie != 0 ) {izdelieid = izdelie;}
	if (section != 0 ) {sectionid = section;}
	if (izdelieid != 0 && sectionid != 0) {loadsection(izdelieid, sectionid);}
	if (sectionid == 7 || sectionid == 5) {loadsection(izdelieid, sectionid);}
	if (sectionid == 'ctrl' && izdelieid == 0) {loadsection(izdelieid, sectionid);}
	//if (sectionid == 'izv') {loadsection(izdelieid, sectionid);}
	if (sectionid == 1) {$('#open_close').css({'display':'none'});}
	else {$('#open_close').css({'display':'block'});}
	if (sectionid == 25) {loadsection(izdelieid, sectionid);}
};

let varframe;
function loadsection(izdelieid, sectionid) {
	switch (sectionid)
	{
	case "1":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "auto";
		refresh('dogovor.php', 'varframe', "&id=" + izdelieid);
	break;

	case "2":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		refresh('docwork.php', 'varframe', "&id=" + izdelieid);
	break;

	case "3":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		document.getElementById("varframe").innerHTML = "";
		refresh('mailbox.php', 'varframe', "&id=" + izdelieid);
	break;

	case "ctrl":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		document.getElementById("varframe").innerHTML = "";
		refresh('mailbox.php', 'varframe', "&id=0");
	break;

	case "innermail":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		document.getElementById("varframe").innerHTML = "";
		//refresh('api/innermail.php', 'varframe', "&id=0");
		console.log(`innermail: ${izdelieid}, ${sectionid}`);
		import("./js/innerMail/innerMail.js?v=<?=time();?>")
			.then(module => {
				// Вызываем функцию из модуля
				module.loadInnerMail(izdelieid);
			})
			.catch(err => console.error("Ошибка загрузки innermail script.js:", err));
	break;

	case "izv":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		document.getElementById("varframe").innerHTML = "";
		refresh('izveshenie.php', 'varframe', "&id=" + izdelieid);
	break;

	case "4":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		refresh('contragent.php', 'varframe', "&id=" + izdelieid);
	break;

	case "5":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		refresh('statis.php', 'varframe', "");
	break;

	case "6":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		refresh('history.php', 'varframe', "&id=" + izdelieid);
	break;

	case "7":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		refresh('findworkdoc.php', 'varframe', "");
	break;

	case "25":
		varframe = document.querySelector("#varframe");
		varframe.style.overflow = "";
		refresh('../gb/', 'varframe', "");
	break;

	default:
		$('#varframe').empty();
	break;
	}
};

var zamok = 0;

var x_ecopass = 0;
function open_edit()
{
	zamok = 1;
	window.openStatus = "1";
	if(sectionid != 1 || x_ecopass==ecopass) {all_open();}
};

function all_open() {
	zamok = 1;
	$('.simplefield').attr('contenteditable', 'true');
	$('.dateinput').removeAttr('readonly');
	$('.checkfield').removeAttr('disabled');
	$('.button_field').css({'display':'block'});
	$('.hide_field').css({'display':'block'});
	$('.nomer').addClass('context-menu-one');
	$('.meganomer').addClass('context-menu-megaclass-one');
	//$('.relocnomer').addClass('context-submenu-megaclass-one');
	$('.relocnomer').addClass('context-document');
	$('.docwork-context').addClass('docwork-context-menu');
	$('.izv-context').addClass('izv-context-menu');
	$('.mail-context').addClass('mail-context-menu');
	$('#open_close').empty()
	.append("<a onclick = 'close_edit();'><img src='include/lock_open.png' alt='открыто'></a>");
	findsave();
};

var ecopass = 22;
function open_eco_edit()
{
	x_ecopass=prompt('Введите пароль:', '');
	if (x_ecopass==ecopass) {
		window.openStatus = "1";
		zamok = 1;
		all_open();
		$('#eco_pass_field').empty()
		.append("<a onclick = 'close_edit();'><img src='include/lock_large_unlocked.png' alt='закрыто'>");
	}
	else alert('Вы ввели неправильный пароль.');
};

function close_edit()
{
	zamok = 0;
	window.openStatus = "0";
	x_ecopass = 0;
	$('.simplefield').attr('contenteditable', 'false');
	$('.dateinput').attr('readonly', 'readonly');
	$('.checkfield').attr('disabled', 'disabled');
	$('.button_field').css({'display':'none'});
	$('.hide_field').css({'display':'none'});
	$('.nomer').removeClass('context-menu-one');
	$('.meganomer').removeClass('context-menu-megaclass-one');
	//$('.relocnomer').removeClass('context-submenu-megaclass-one');
	$('.relocnomer').removeClass('context-document');
	$('.docwork-context').removeClass('docwork-context-menu');
	$('.izv-context').removeClass('izv-context-menu');
	$('.mail-context').removeClass('mail-context-menu');
	$('#open_close').empty()
	.append("<a onclick = 'open_edit();'><img src='include/lock.png' alt='закрыто'></a>");
	$('#eco_pass_field').empty()
		.append("<a onclick = 'open_eco_edit();'><img src='include/lock_large_locked.png' alt='закрыто'>");
};

function okno_show(htag, getdata){       //field_id = id_table_izdelie
	console.log("(okno_show) getdata: " + getdata);
	$( "#" + htag ).empty();
	$( "#" + htag ).dialog( "open" );
	refresh("uplfiles.php", htag, getdata);
};

function okno_settings(htag, getdata){       //field_id = id_table_izdelie
	console.log("(okno_show) getdata: " + getdata);
	$( "#" + htag ).empty();
	$( "#" + htag ).dialog( "open" );
	refresh("settings.php", htag, getdata);
};

function upload(uplname, getdata){      //add_data = filter_id
	new AjaxUpload(uplname, {
		action: 'upload-file.php?getdata=' + getdata,
		name: 'uploadfile',
		onSubmit: function(file, ext){
			$( "#dialog" ).empty();
		},
		onComplete: function(file, response){
			console.log("(upload)");
			refresh("uplfiles.php", "dialog", getdata);
			loadsection(izdelieid, sectionid);
		}
	});
};

function newlocate(func, table, newdetid, id, newpos){
	var sovlemove = confirm("Перенести запись в раздел '" + newpos + "' ? ");
	if (sovlemove==true) {
		$.ajax({
			url: 'function.php',				// php файл в который отправляется запрос
			type: 'POST',				//метод POST
			data: {
				relocate: func,
				table: table,
				newdetid: newdetid,
				id: id		
			},				
			success:function (data) {      	//в случае удачи выполения запросов выполняется
				loadsection(izdelieid, sectionid);
			}
		});
	}
};


</script>

<div id = "leftmenu_box">
	<div class = 'menuitem_header'></div>
	<?include('divtree.php');?>
	<div id = "menu_edit_field" class = 'button_field button_layer' style = 'display:none;float:left;'>
		<a onClick = "okno_settings('dialog','&htag=dialog&dbtable=izdelie');"><img src = 'include/new window.png' title = ''></a>
	</div>
</div>

<div id = "right_box">
	<div id = "topmenu_box">
		<div>
			<div class = 'topmenuitem' onClick="menuchoice('0','1');">Договорные документы</div>
			<div class = 'topmenuitem' onClick="menuchoice('0','2');">Документы и работа</div>
			<div class = 'topmenuitem' onClick="menuchoice('0','izv');">Извещения</div>
			<div class = 'topmenuitem' onClick="menuchoice('0','3');">Переписка</div>
			<div class = 'topmenuitem btnInnerMail' onClick="menuchoice('0','innermail');">Внутренняя переписка</div>
			<div class = 'topmenuitem' style = "color:red;" onClick="menuchoice('0','ctrl');">На контроле</div>
			<div class = 'topmenuitem' onClick="menuchoice('0','4');">Контрагенты</div>
			<!--<div class = 'topmenuitem' onClick="menuchoice('0','5');">Статистика</div>-->
			<div class = 'topmenuitem' onClick="menuchoice('0','6');">Описание</div>
			<div class = 'topmenuitem' onClick="menuchoice('0','7');">Выборка документов</div>
		</div>
		<div class="space_filer"></div>
		<div id = "open_close"><a onclick = 'open_edit();'><img src='include/lock.png' alt='закрыто'></a></div>
		<div id="menu_box"></div>
	</div>
	<div id = "varframe"></div>
</div>



<div id="dialog" title="Dialog Title"></div>

<div id="settings" title="Настройки"></div>

<script>

$(document).ready(function() {

	$('.menuitem').click(function () {
		$('.menuitem').removeClass('menuactive');
		$(this).addClass('menuactive');
	});

	$('.topmenuitem').click(function () {
		$('.topmenuitem').removeClass('menuactive');
		$(this).addClass('menuactive');
	});

$.contextMenu({  //меню удаления
    selector: '.context-menu-megaclass-one',
    items: {
        delete: {
			name: 'Удалить',
			callback: function(key, options) {
				console.log("(Удалить файл) htag: " + $(this).data('htag') + "; table: " + $(this).data('table') + "; id: " + $(this).data('id') + "; getdata: " + $(this).data('getdata') + "; file: " + $(this).data('actfile'));
				delete_refresh($(this).data('table'), $(this).data('id'), $(this).data('actfile'), $(this).data('htag'), $(this).data('getdata'));
				//loadsection(izdelieid, sectionid);
			}
		},
        sep1: '---------',
        quit: {
			name: 'Выйти',
			callback: function(key, options) {}
		}
    }
});

function getIzdList(lineCount = 20) {
	let blocks = {}, line = {}, j = 0;
	const izdList = document.querySelectorAll('.tree_item.menuitem');
	izdList.forEach((item, i) => {
		line[`lineName_${i}`] = {
			name: `${item.innerText}`, callback: function() {
				newlocate('move', this[0].dataset.table, item.dataset.izdnomer, this[0].dataset.id, item.innerText)
			}
		}
		if(i%lineCount == 0 && i > 0) {
			blocks[`block_${j++}`] = {name: `${(j-1)*lineCount+1} - ${i}`, items: line}
			line = {};
		}
	})
	blocks[`block_${j++}`] = {name: `${(j-1)*lineCount+1} - ${izdList.length}`, items: line}

	return {name: "Переместить", items: blocks};
}

/* структура меню плагина $.contextMenu
	let obj = {
		name: "catalog",
		items: {

			"блок1": {
				name: "список 1",
				items: {
					"имя": {name: "имя3", callback: () => {}},
					"имя1": {name: "имя4", callback: () => {}},
				}
			},

		}
	}
*/


const menuItem_Delete = {
	name: 'Удалить',
	callback: function(key, options) {
		console.log("(Удалить строку) htag: " + $(this).data('htag') + "; table: " + $(this).data('table') + "; id: " + $(this).data('id') + "; getdata: " + $(this).data('getdata') + "; file: " + $(this).data('actfile'));
		delete_refresh($(this).data('table'), $(this).data('id'), $(this).data('actfile'), $(this).data('htag'), $(this).data('getdata'));
	}
}

const menuItem_movetoizv = {
	name: "Перевести в раздел 'Извещение'",
	callback: function(key, options) {
		changeDoctype('1', this[0].dataset.id);
		loadsection(izdelieid, sectionid);
		console.log("(Перевести в Извещение) htag: " + $(this).data('htag') + "; table: " + $(this).data('table') + "; id: " + $(this).data('id') + "; getdata: " + $(this).data('getdata') + "; file: " + $(this).data('actfile'));
	}
}

const menuItem_movetodoc = {
	name: "Перевести в раздел 'Документы'",
	callback: function(key, options) {
		changeDoctype('0', this[0].dataset.id);
		loadsection(izdelieid, sectionid);
		console.log("(Перевести в Документы) htag: " + $(this).data('htag') + "; table: " + $(this).data('table') + "; id: " + $(this).data('id') + "; getdata: " + $(this).data('getdata') + "; file: " + $(this).data('actfile'));
	}
}

const menuItem_movetoInnermail = {
	name: "Перенести в раздел 'Внутренняя переписка'",
	callback: function() {
		import("./js/commonTableFnc/dataTransfer.js?v=<?=time();?>")
		.then(module => {
			// Вызываем функцию из модуля
			const data = {
				table: this[0].dataset.table,
				column: 'hide',
				id: this[0].dataset.id,
				content: 2,
				fl: 'txtSave',
			}
			module.dataTransfer(data).then(dt => {
				loadsection(izdelieid, sectionid);
			});
		})
		.catch(err => console.error("Ошибка загрузки innermail script.js:", err));
	}
}

$.contextMenu({  //меню удаления с подменю
    selector: '.context-document',
    items: {
        delete: menuItem_Delete,
		movetoizv: menuItem_movetoizv,
		movetodoc: menuItem_movetodoc,
		move: getIzdList(),
        sep1: '---------',
        quit: {
			name: 'Выйти',
			callback: function(key, options) {}
		}
    }
});

$.contextMenu({  //меню удаления с подменю
    selector: '.docwork-context-menu',
    items: {
        delete: menuItem_Delete,
		movetoizv: menuItem_movetoizv,
		move: getIzdList(),
        sep1: '---------',
        quit: {
			name: 'Выйти',
			callback: function(key, options) {}
		}
    }
});

$.contextMenu({  //меню удаления с подменю
    selector: '.izv-context-menu',
    items: {
        delete: menuItem_Delete,
		movetodoc: menuItem_movetodoc,
		move: getIzdList(),
        sep1: '---------',
        quit: {
			name: 'Выйти',
			callback: function(key, options) {}
		}
    }
});

$.contextMenu({  //меню удаления с подменю
    selector: '.mail-context-menu',
    items: {
		delete: menuItem_Delete,
		moveToInnermail: menuItem_movetoInnermail,
		move: getIzdList(),
        sep1: '---------',
        quit: {
			name: 'Выйти',
			callback: function(key, options) {}
		}
    }
});


$( "#dialog" ).dialog({
	autoOpen: false,
	width: 700,
	height: 350,
	buttons: [
		{
			text: "Ok",
			click: function() {
				$( this ).dialog( "close" );
			}
		},
		{
			text: "Cancel",
			click: function() {
				$( this ).dialog( "close" );
			}
		}
	]
});

window.onscroll = function(){
    var some_block = document.getElementById('zagolovok');
    var nav = document.querySelector('#zagolovok + #poledlyafix');
    if(window.pageYOffset > some_block.clientHeight)
        nav.style.position = 'fixed';
    else
        nav.style.position = '';
}



});


</script>

    </body>
</html>

