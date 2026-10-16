import { state } from "./commonTableFnc/state.js";
import { varIndexCtrl } from "./commonTableFnc/varIndexCtrl.js";
import { loadContrAgent } from "./contrAgent/contrAgent.js";
import { loadControlMail } from "./controlMail/controlMail.js";
import { divtree } from "./divtree/divtree.js";
import { docwork } from "./docwork/docwork.js";
import { loadInnerMail } from "./innerMail/innerMail.js";
import { izv } from "./izv/izv.js";
import { loadOuterMail } from "./outerMail/outerMail.js";

const hTxt = /*html*/`
    <div id = "leftmenu_box">
	    <div class = "menuitem_header"></div>
        <div id = "base_layer">
        
            <div id = "tree_choice_layer">
                <div id = "tree_button_list" class = "tree_select"><div></div></div>
                <div id = "tree_button_catalog" class = "tree_select"><div></div></div>
                <div id = "tree_button_favorites" class = "tree_select"><div></div></div>
            </div>

            <div id = "tree_input_box">
                <input type="text" style="width:90%;" id="filter_izdelie_dogovor" placeholder="Индекс или договор:" title = "Введите индекс изделия или номер договора">
            </div>

            <div id = "tree_layer" class = "tree_layer">
                <span></span>
            </div>

        </div>

        <div id = "tree_edit_layer">
                <div id = "tree_edit" class = "tree_wait"></div>
                <div id = "h_button" class = "notshow_any"><div id = "create_new_cat"></div></div>
        </div>

        <div id = "menu_edit_field" class = "button_field button_layer" style = "display:none;float:left;">
            <a onClick = "okno_settings('dialog','&htag=dialog&dbtable=izdelie');"><img src = "include/new window.png" title = ""></a>
        </div>
    </div>

    <div id = "right_box">
        <div id = "topmenu_box">
            <div>
                <div class = "topmenuitem" data-section="1">Договорные документы</div>
                <div class = "topmenuitem" data-section="2">Документы и работа</div>
                <div class = "topmenuitem" data-section="izv">Извещения</div>
                <div class = "topmenuitem" data-section="3">Переписка</div>
                <div class = "topmenuitem" data-section="innermail">Внутренняя переписка</div>
                <div class = "topmenuitem" data-section="ctrl">На контроле</div>
                <div class = "topmenuitem" data-section="4">Контрагенты</div>
                <div class = "topmenuitem" data-section="6">Описание</div>
                <div class = "topmenuitem" data-section="7">Выборка документов</div>
            </div>
            <div class="space_filer"></div>
            <div id = "open_close"><a onclick = "open_edit();"><img src="include/lock.png" alt="закрыто"></a></div>
            <div id="menu_box"></div>
        </div>
        <div id = "varframe"></div>
    </div>

    <div id="dialog" title="Dialog Title"></div>

    <div id="settings" title="Настройки"></div>
`;

state.loadHtml("body", hTxt);


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

window.update_db = (id_pole_table,contentsave) => {   // функция для сохранения отредактированного текста с помощью ajax
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


window.add_refresh = (table, field, fieldvalue, loadfile, htag, getdata) => {
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
            const menu = document.querySelector("#tree_layer .menuactive")?.dataset.izdnomer;
            const section = document.querySelector("#topmenu_box .menuactive")?.dataset.section;
            loadsection(menu, section);
		}
	
	});
}; 

var izdelieid=0;
var sectionid=0;

varIndexCtrl();

function loadsection(id, section) {
    const layer = document.querySelector("#varframe");
    layer.style.overflow = "";
    const select = {
        "1": () => {
            layer.style.overflow = "auto";
            refresh('dogovor.php', 'varframe', "&id=" + id);
        },
        "2": () => docwork(id),
        "3": () => loadOuterMail(id),        
        "ctrl": () => loadControlMail(),        
        "innermail": () => loadInnerMail(id),        
        "izv": () => izv(id),        
        "4": () => loadContrAgent(id),        
        "6": () => refresh('history.php', 'varframe', "&id=" + id),        
        "7": () => refresh('findworkdoc.php', 'varframe', ""),
    }
    select[section]?.();

};

window.zamok = 0;

window.x_ecopass = 0;
window.open_edit = () => {
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
window.open_eco_edit = () => {
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

window.close_edit = () => {
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

window.okno_show = (htag, getdata) => {       //field_id = id_table_izdelie
	console.log("(okno_show) getdata: " + getdata);
	$( "#" + htag ).empty();
	$( "#" + htag ).dialog( "open" );
	refresh("uplfiles.php", htag, getdata);
};

window.okno_settings = (htag, getdata) => {       //field_id = id_table_izdelie
	console.log("(okno_show) getdata: " + getdata);
	$( "#" + htag ).empty();
	$( "#" + htag ).dialog( "open" );
	refresh("settings.php", htag, getdata);
};

window.upload = (uplname, getdata) => {      //add_data = filter_id
	new AjaxUpload(uplname, {
		action: 'upload-file.php?getdata=' + getdata,
		name: 'uploadfile',
		onSubmit: function(file, ext){
			$( "#dialog" ).empty();
		},
		onComplete: function(file, response){
			console.log("(upload)");
			refresh("uplfiles.php", "dialog", getdata);
            const menu = document.querySelector("#tree_layer .menuactive")?.dataset.izdnomer;
            const section = document.querySelector("#topmenu_box .menuactive")?.dataset.section;
            loadsection(menu, section);
		}
	});
};


divtree();

const body = document.querySelector("body");
body.addEventListener("click", (e) => {
    if(e.target.closest("#tree_layer .menuitem")) {
        body.querySelector(".menuitem.menuactive")?.classList.remove("menuactive");
        e.target.classList.add("menuactive")
        console.log("e.target", e.target);
    }
    if(e.target.closest("#topmenu_box .topmenuitem")) {
        body.querySelector(".topmenuitem.menuactive")?.classList.remove("menuactive");
        e.target.classList.add("menuactive")
        console.log("e.target", e.target);
    }
 
    if(e.target.closest("#tree_layer .menuitem") || e.target.closest("#topmenu_box .topmenuitem")) {
        const menu = document.querySelector("#tree_layer .menuactive")?.dataset.izdnomer;
        const section = document.querySelector("#topmenu_box .menuactive")?.dataset.section;
        menu && section && loadsection(menu, section);
    }
})

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

