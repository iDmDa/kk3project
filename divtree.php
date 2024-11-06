<!DOCTYPE html>
<HTML lang="ru">
<head>
<meta charset="utf-8">
<style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    html, body {
        padding: 0;
        margin: 0;
        scrollbar-width: thin;
    }

    #base_layer {
        display: flex;
        flex-direction: column;
        justify-content:left;
        align-items: left;
        background-color: azure;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    #tree_choice_layer {
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
        padding-left: 6px;
        padding-right: 6px;
        margin-top: 5px;
    }

    #tree_choice_layer>div {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
        width: 100%;
        margin-left: 2px;
        margin-right: 2px;
    }

    #tree_choice_layer>div img {
        height: 20px;
    }

    #tree_layer {
        /*background-color: beige;*/
        width: 150px;
        font-family: "Trebuchet MS", Helvetica, sans-serif;
        font-size: 12px;
        font-weight: 600;
        overflow:scroll;
        scrollbar-width: thin;
        padding-bottom: 70px;
    }

    #tree_input_box {
        display: flex;
        flex-direction: row;
        justify-content: center;
        margin-top: 5px;
    }

    .tree_layer, .tree_catalog, .tree_item {
        user-select: none;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .tree_catalog span {
        font-weight: 600;
        font-size: 13px;
        text-shadow: 0 0 1px blue;
        text-decoration: underline;
        /*color: rgb(131, 10, 46);*/
    }

    .tree_item {
        padding-left: 5px;
        /*transition: all .2s;*/
        margin-top: 5px;
        margin-bottom: 5px;
        margin-left: 5px;
    }

    .tree_wait {
        background-image: url('include/tree_wait.png');
        background-size: 16px;
        display: inline-block;
        width: 16px;
        height: 16px;
        content:"";
    }

    #create_new_cat {
        background-image: url('include/new-document.png');
        background-size: 16px;
        display: inline-block;
        width: 16px;
        height: 16px;
        content:"";
    }

    .tree_start_edit {
        background-image: url('include/tree_start_edit.png');
        background-size: 16px;
        display: inline-block;
        width: 16px;
        height: 16px;
        content:"";
    }

    .tree_item_ico:before {
        background-image: url('include/list.png');
        background-size: 12px;
        display: inline-block;
        width: 12px;
        height: 12px;
        content:"";
    }

    .tree_delete_cat span{
        color: orangered;
    }
    .tree_delete_cat span:after {
        background-image: url('include/delete.png');
        background-size: 12px;
        margin-left: 5px;
        display: inline-block;
        width: 12px;
        height: 12px;
        content:"";
    }

    .open_catalog:before {
        background-image: url('include/minus.png');
        background-size: 12px;
        display: inline-block;
        width: 12px;
        height: 12px;
        content:"";
    }

    .close_catalog>.tree_item {
        max-height: 0;
        transition: max-height 0.3s cubic-bezier(0, 1, 0, 1); 
        margin: 0;
        overflow: hidden;
    }

    .open_catalog>.tree_item {
        overflow: hidden;
        max-height: 2000px;
        transition: max-height 0.3s cubic-bezier(1, 0, 1, 0);
    }

    .close_catalog:before {
        background-image: url('include/plus.png');
        background-size: 12px;
        display: inline-block;
        width: 12px;
        height: 12px;
        content:"";
    }

    #tree_edit_layer {
        width: 150px;
        display: flex;
        flex-direction: row;
        justify-content: space-around;
    }

    .tree_select, .button_style {
        background: linear-gradient(to top, #BAC1E1, #D2D5E1);
        border: solid 1px DarkBlue;
        border-radius: 6px; 
        max-width:100%;
        padding: 5px;	/*поля внутри элемента; */
        font-family: "Trebuchet MS", Helvetica, sans-serif; 
        font-size:12px;
        font-weight: 600;
    }

    .tree_select div {
        filter: grayscale(100%);
    }

    .button_style {
        text-align: center;
        padding: 2px;
    }
    .button_style:hover {
        background: linear-gradient(to top, #D2D5E1, #BAC1E1);
    }

    .tree_select:hover {
        background: linear-gradient(to top, #D2D5E1, #BAC1E1);
    }

    .tree_select:hover div {
        filter: grayscale(0%);
    }

    #tree_button_list div {
        background-image: url('tree_list.png');
        background-size: 20px;
        display: inline-block;
        width: 20px;
        height: 20px;
        content:"";

    }    
    
    #tree_button_catalog div {
        background-image: url('tree_catalog.png');
        background-size: 20px;
        display: inline-block;
        width: 20px;
        height: 20px;
        content:"";
    }

    #tree_button_favorites div {
        background-image: url('tree_star.png');
        background-image: url('svg_star.svg');
        background-size: 20px;
        display: inline-block;
        width: 20px;
        height: 20px;
        content:"";
    }

    .tree_menu_active {
	background: linear-gradient(to right, lime, lime);
    }

    .tree_menu_active div {
        filter: grayscale(0%);
    }

    .hide_item, .hide_catalog {
        display: none;
    }

    .tree_favorites {
        border-color: goldenrod!important;
        border-style: solid!important;
        border: 2px;
    }

</style>
</head>
<body>

<?
    include("dbdata.php");
    $mysqli = new mysqli($db_host, $db_login, $db_pass, $db_name);
    if ($mysqli->connect_errno) {
    echo "Нет связи с базой данных!";
    exit;
    }

    $sql = "SELECT izdelie.id, izdelie.name, izdelie.catalog, dogovor.nomer from izdelie LEFT JOIN dogovor ON izdelie.id=dogovor.detid where izdelie.hide <> 1";
    $r = $mysqli->query($sql);
    $row = $r->fetch_all(MYSQLI_ASSOC);

    $sql = "select * from catalog";
    $r = $mysqli->query($sql);
    $cat_name = $r->fetch_all(MYSQLI_ASSOC);

    $mysqli->close();



?>

<div id = "base_layer">
    
    <div id = "tree_choice_layer">
        <div id = "tree_button_list" class = "tree_select"><div></div></div>
        <div id = "tree_button_catalog" class = "tree_select"><div></div></div>
        <div id = "tree_button_favorites" class = "tree_select"><div></div></div>
    </div>

    <div id = "tree_input_box">
        <input type='text' style='width:90%;' id='filter_izdelie_dogovor' placeholder='Индекс или договор:' title = 'Введите индекс изделия или номер договора'>
    </div>

    <div id = "tree_layer" class = "tree_layer"><span></span>
        <?  //Считывание всех каталогов и элементов
            foreach($cat_name as $key => $value) {
                echo "<div id = '" .$value['id'] ."_name_catalog' class = 'tree_item tree_catalog' data-catalog='" .$value['catalog'] ."'><span>" .$value['name'] ."</span></div>";
            };

            foreach($row as $key => $value) {
                //echo "<div id = '" .$value['id'] ."_name_izdelie' class = 'tree_item menuitem' onClick=menuchoice('" .$value['id'] ."','0'); data-catalog='" .$value['catalog'] ."' data-dogovor='" .$value['nomer'] ."' data-izdnomer='" .$value['id'] ."'>" .$value['name'] ."</div>";
                echo "<div id = '" .$value['id'] ."_name_izdelie' class = 'tree_item menuitem' data-catalog='" .$value['catalog'] ."' data-dogovor='" .$value['nomer'] ."' data-izdnomer='" .$value['id'] ."'>" .$value['name'] ."</div>";
            };
        ?>
    </div>

</div>
<div id = "tree_edit_layer">
        <div id = "tree_edit" class = "tree_wait"></div>
        <div id = 'h_button' class = 'notshow_any'><div id = "create_new_cat"></div></div>
</div>

<script>

    let edit_button = document.querySelector('#tree_edit');
    let create_new_cat = document.querySelector('#create_new_cat');
    let tree = document.getElementById('tree_layer');
    let h_button = document.getElementById('h_button');
    let start_point;
    let move_point;
    let tree_input_find = document.getElementById('filter_izdelie_dogovor');

    function edit_button_click() { //Включение/выключение редактирования дерева
        edit_button.classList.toggle('tree_wait');
        edit_button.classList.toggle('tree_start_edit');
        h_button.classList.toggle('notshow_any');


        scrollbar_destroy();
        //scrollbar_create();
        //$(".tree_layer").mCustomScrollbar('destroy');
        //let msc = document.querySelector('#menuframe');
        //msc.style.overflow = null;

        document.getElementById('tree_edit_layer').classList.contains('tree_start_edit') ? none : document.getElementById('tree_edit_layer').style.display = 'none';

        if(edit_button.classList.contains('tree_start_edit')) {
            document.querySelector('#tree_layer').children[0].innerHTML = '&mdash;&mdash;&mdash;&mdash;&mdash;';
            document.querySelector('#tree_layer').style.overflow = '';
            edit_tree_fnc_list();


            document.onkeydown = function() {
                let all_cat = document.querySelectorAll('.tree_catalog');
                if(event.ctrlKey) all_cat.forEach(function(itm){
                    itm.classList.add('tree_delete_cat');
                    tree.onclick = function(){
                        if(event.target.localName == 'span' && event.target.parentElement.children.length == 1) {
                            if(confirm('Удалить?')) {
                                let data = new FormData();
                                data.append('delete_item', true);
                                data.append('id', event.target.parentElement.id.split('_')[0]);
                                tree_save(data, event.target.parentElement.remove());
                                all_cat.forEach(function(itm){itm.classList.remove('tree_delete_cat')}); //удалить метки для удаления каталогов
                            };
                            tree.onclick = tree_click;
                        }
                        else {
                            alert('Каталог не пуст!');
                            tree.onclick = tree_click;
                        };
                    };
                });
            };
            document.onkeyup = function() {
                let all_cat = document.querySelectorAll('.tree_catalog');
                if(event.key == 'Control') {
                    all_cat.forEach(function(itm){itm.classList.remove('tree_delete_cat')});
                    tree.onclick = tree_click;
                }
            };
        };

        if(edit_button.classList.contains('tree_wait')) {
            document.querySelector('#tree_layer').children[0].innerHTML = '';
            edit_tree_removefnc_list();
            document.onkeydown = '';
            document.onkeyup = '';
            scrollbar_create();
        };
    };

    function create_new_catalog() {
        let div = document.createElement('div');
        let span = document.createElement('span');
        div.append(span);
        span.innerText = 'Новый каталог';
        div.classList.add('tree_item');
        div.classList.add('tree_catalog');
        div.classList.add('open_catalog');

        let data = new FormData();
        data.append('create_new_cat', true);
        data.append('name', 'name');
        data.append('value', span.innerText);

        let xhr = new XMLHttpRequest();
         xhr.open('POST', 'sv.php', true);
         xhr.send(data);
         xhr.onload = function () {
            div.id = xhr.responseText + '_name_catalog';
            tree.appendChild(div);
         };  

    };


    //Установка состояния каталогов
    let catalog = document.querySelectorAll('.tree_catalog');
    catalog.forEach(function(item) {
        if(!localStorage.getItem(item.id)) item.classList.add('close_catalog');
        if(localStorage.getItem(item.id)) item.classList.add(localStorage.getItem(item.id));
    });

    function tree_catalog_create() {  //Распределение по каталогам в соотетствии с выведенными данными из БД
        let tree_item = document.querySelectorAll('.tree_item');  //Поиск всех строк и каталогов
        tree_item.forEach(function(item) {
            if(item.dataset.catalog != "") {
                let catalog = document.getElementById(item.dataset.catalog + "_name_catalog"); //Получение каталога по параметру data-catalog строки
                catalog ? catalog.appendChild(item) : null;  //Перемещение строки в каталог
            };
        });
    };

    function tree_sort() {  //Сортировка по алфавиту строк и каталогов на странице (без перезапроса в БД)
        let sort_item = document.querySelectorAll('.tree_item');
        let itemsArray = [];

        for (let i = 0; i < sort_item.length; i++) {  //Получение массива из дерева на странице
            itemsArray[i] = [sort_item[i].parentElement, sort_item[i].parentElement.removeChild(sort_item[i])]; //массив с 2 колонками: каталог и элемент
        }
        
        itemsArray.sort(function(a, b) { //Сортировка массива по тексту строки/каталога
            if (a[1].innerText.trim() < b[1].innerText.trim()) return -1;
            if (a[1].innerText.trim() > b[1].innerText.trim()) return 1;
            return 0;
        });

        let parent_dir = tree; //document.getElementById('tree_layer');
        while (itemsArray.length > 0) {
            let asm = itemsArray.find(function(item, i){ //Поиск первого значения из массива по условию
                let catalog = item[0];
                if(catalog == parent_dir) {
                    //console.log(itemsArray[i]);
                    itemsArray.splice(i, 1);  //удаление с позиции i 1 элемента из списка массива
                    return true;
                }
            });
            //console.log(asm);
            parent_dir.appendChild(asm[1]);

            let item_count = itemsArray.filter(function(item){ //Проверка остались ли в массиве строки с верхним каталогом
                if(item[0] == parent_dir) return true;});
            //console.log(item_count);

            if(item_count == 0) try {parent_dir = itemsArray[0][0];} catch {}  //Переназначение нового верхнего каталога
        };
    };

    function tree_list() { //Отображение списка, каталоги скрыты
        let items = document.querySelectorAll('.tree_item');
        let catalogs = document.querySelectorAll('.tree_catalog');
        let parent_dir = tree;
        items.forEach(function(item, i){
            parent_dir.appendChild(item);
            item.classList.remove('tree_item_ico');
            item.classList.remove('hide_item');
        });
        catalogs.forEach(function(item, i){
            item.classList.add('hide_catalog');
        });
    };

    function tree_catalog() { //Отображение списка в виде каталога, у строк добавлена иконка документа
        let items = document.querySelectorAll('.tree_item');
        let catalogs = document.querySelectorAll('.tree_catalog');
        items.forEach(function(item, i){
            item.classList.add('tree_item_ico');
            item.classList.remove('hide_item');
        });
        catalogs.forEach(function(item, i){
            item.classList.remove('hide_catalog');
        });
    };

    function tree_favorites() { //Отображение только строк с классом избранное, остальное скрыто
        let items = document.querySelectorAll('.tree_item');
        let catalogs = document.querySelectorAll('.tree_catalog');
        let fav_item = document.querySelectorAll('.tree_favorites');

        catalogs.forEach(function(item, i){
            item.classList.add('hide_catalog');
        });

        items.forEach(function(item, i){
            //item.classList.remove('tree_item_ico');
            item.classList.add('hide_item');
        });
        
        fav_item.forEach(item => item.classList.remove('hide_item'));
    };

    function tree_item_classlist() {
        let items = document.querySelectorAll('.tree_item');
        items.forEach(function(item){
            localStorage.getItem(item.id) == 'favorite' ? item.classList.add('tree_favorites') : item.classList.remove('tree_favorites');
        });
    };

    function tree_save(data, exec)
      {
         //let data = new FormData();
         //data.append(action, value);

         let xhr = new XMLHttpRequest();
         xhr.open('POST', 'sv.php', true);
         xhr.send(data);
         xhr.onload = function () {
            exec;
         };        
      }


    function evnt_limit(e, stop_list) //Ограничение на выбор точки события
    {
        let elem = e.target;
        let intersection = false;

        do {
            stop_list.forEach(function(item, i , stop_list){
                if(elem.classList.contains(item)) intersection = true;
            });
            if(intersection) break;
            if(elem.classList.contains('tree_layer')) break;
        } while(elem = elem.parentElement);

        return elem;
    };

    function tree_mousedown() { //Установка свойств (по левому клику) включающих перемещение элемента
        if(event.which == 1) {
            start_point = evnt_limit(event, ['tree_item', 'tree_catalog']);
            start_point.draggable = true;    
        };
    };

    function tree_mouseup() { //Удаление свойств draggable при отжатии левой кнопки
        if(start_point) start_point.draggable = false;
    };

    function tree_dragstart() { //Событие начала перемещения
        start_point.style.color = 'gray';
    };

    function tree_dragend() { //Событие окончания перемещения
        start_point.style.color = '';
        move_point.children[0].style.color = '';
        start_point.draggable = false;

        let elem = move_point;
        do {
            if(elem.classList.contains('tree_catalog')) break;
            if(elem.classList.contains('tree_layer')) break;
            elem = elem.parentElement;
        } while(elem.id != 'tree_layer')

        let data = new FormData();
        data.append('move_item', true);
        data.append('item', start_point.id.split('_')[0]);
        data.append('table', start_point.id.split('_')[2])
        elem.id == 'tree_layer' ? data.append('catalog', '0') : data.append('catalog', elem.id.split('_')[0]);
        tree_save(data, elem.appendChild(start_point));
        
        tree_sort();
    };

    function tree_dragover() { //Свойства принимающего блока над которым находится перетаскиваемый элемент
        event.preventDefault();
        if(start_point != event.target) {
            move_point = evnt_limit(event, ['tree_catalog']);
            move_point.children[0].style.color = 'lime';           
        }
    };

    function tree_dragleave() { //Событие при покидании принимающего блока мышки с перетаскиваемым элементом
        if(event.target != start_point) move_point.children[0].style.color = '';
    };

    function tree_click(event) { //Функция открытия/закрытия каталогов
        let etcl = event.target.classList;
        if(etcl.contains('open_catalog')) {
            etcl.remove('open_catalog');
            etcl.add('close_catalog');
            localStorage.setItem(event.target.id, 'close_catalog');
            //let all_item = event.target.querySelectorAll('.tree_item');
            //all_item.forEach(element => element.classList.toggle('menuitem'));
            return;
        }
        if(etcl.contains('close_catalog')) {
            etcl.remove('close_catalog');
            etcl.add('open_catalog');
            localStorage.setItem(event.target.id, 'open_catalog');
            //let all_item = event.target.querySelectorAll('.tree_item');
            //all_item.forEach(element => element.classList.toggle('menuitem'));
            return;
        }
    };

    let context_element;
    function tree_contextmenu(event) { //Переименование каталогов
        
        if(event.target.localName == 'span') {
            
            if(context_element) {
                context_element.style.color = '';
                context_element.setAttribute('contenteditable', 'false');
                
                complete_fnk();
            };

            context_element = event.target;
            context_element.setAttribute('contenteditable', 'true');
            context_element.style.color = 'OrangeRed';
            edit_tree_removefnc_list();
            tree.onclick = '';

            context_element.onblur = function() {
                context_element.style.color = '';
                context_element.setAttribute('contenteditable', 'false');
                tree_sort();
                complete_fnk();
            };

            context_element.onkeydown = function(event) {
                if(event.key == 'Enter' || event.key == 'Escape') {
                    context_element.style.color = '';
                    context_element.setAttribute('contenteditable', 'false');
                    tree_sort();
                    complete_fnk();
                }
            };

            complete_fnk = function () {
                context_element.onkeydown = '';
                context_element.onmousedown = '';
                context_element.onblur = '';
                tree.onclick = tree_click;

                let data = new FormData();
                data.append('rename', true);
                data.append('name', context_element.innerText);
                data.append('id', context_element.parentElement.id.split('_')[0]);
                tree_save(data, '');

                edit_tree_fnc_list();
            };
        }
        return false;
    };

    function edit_tree_fnc_list() {
        tree.onmousedown = tree_mousedown;
        tree.onmouseup = tree_mouseup;
        tree.ondragstart = tree_dragstart;
        tree.ondragend = tree_dragend;
        tree.ondragover = tree_dragover;
        tree.ondragleave = tree_dragleave;
        tree.ondrag = function (event) {};
        tree.oncontextmenu = tree_contextmenu;
    };

    function edit_tree_removefnc_list() {
        tree.onmousedown = '';
        tree.onmouseup = '';
        tree.ondragstart = '';
        tree.ondragend = '';
        tree.ondragover = '';
        tree.ondragleave = '';
        tree.ondrag = '';
        tree.oncontextmenu = '';
    };

    function scrollbar_destroy () {
        $(".tree_layer").mCustomScrollbar('destroy');
    };

    function scrollbar_create () {
        $(".tree_layer").mCustomScrollbar({
            axis: "y",              // вертикальный скролл
            theme: "minimal-dark",  // тема
            scrollInertia: "300",   // продолжительность прокрутки, значение в миллисекундах
            setHeight: "100%",      // высота блока (переписывает CSS)
            mouseWheel: {
                deltaFactor: 100    // кол-во пикселей на одну прокрутку колёсика мыши
            }
        });
    };

    function tree_buton_settings () {
        document.getElementById('tree_input_box').removeAttribute('style'); //Вернуть поисковую строку
        document.getElementById('add_fav_but') != null ? document.getElementById('add_fav_but').remove() : null; //удалить кнопку 'добавить/удалить' избранное
    };

    function choise_tree_item_window () {

        let div_max = document.createElement('div');
        let div_content = document.createElement('div');
        let div_header = document.createElement('div');
        let div_box = document.createElement('div');

        div_max.style.width = '100%';
        div_max.style.height = '100%';
        div_max.style.zIndex = '100';
        div_max.style.position = 'absolute';
        div_max.id = 'choise_layer';
        div_max.style.opacity = '0.5';
        div_max.style.background = 'grey';

        div_box.style.left = '100px';
        div_box.style.right = '100px';
        div_box.style.top = '100px';
        div_box.style.bottom = '100px';
        div_box.style.backgroundColor = '#c0d6ff';
        div_box.style.position = 'fixed';
        div_box.style.zIndex = '101';
        div_box.style.overflow = 'auto';
        div_box.style.display = 'flex';
        div_box.style.flexDirection = 'column';
        div_box.id = "choise_window";
        div_box.style.border = '1px';
        div_box.style.borderStyle = 'solid';

        div_header.innerText = 'Избранное';
        div_header.classList.add('table_big_header');
        div_header.classList.add('autotable');
        div_header.style.border = '0px';
        div_header.style.borderBottom = '1px';
        div_header.style.borderStyle = 'solid';

        div_content.style.display = 'flex';
        div_content.style.overflow = 'auto';
        div_content.style.flexDirection = 'column';
        div_content.style.flexWrap = 'wrap';

        document.body.append(div_max);
        document.body.append(div_box);
        div_box.append(div_header);
        div_box.append(div_content);

        let bs_div = document.querySelectorAll('.tree_item');

        bs_div.forEach(function(item, i) {
            let new_item = item.cloneNode(true);
            new_item.classList.remove('hide_item');
            new_item.style.width = '150px';
            div_content.append(new_item);
        });

        let remove_fnc = function(event){
            console.log(event.target.id);
            if(event.keyCode == 27  || event.target.id == 'choise_layer') {
                document.getElementById('choise_layer').remove();
                document.getElementById('choise_window').remove();
                document.body.removeEventListener('keydown', remove_fnc);
                tree_switch ('tree_button_favorites');
                tree_switch ('tree_button_favorites');
            }
        };

        document.body.addEventListener('keydown', remove_fnc);
        div_max.addEventListener('click', remove_fnc);
        div_content.addEventListener('click', function(event) {
            console.log(event.target.classList);
            if(event.target.classList.contains('tree_item')) {
                if(event.target.classList.contains('tree_favorites')) {
                    event.target.classList.remove('tree_favorites');
                    localStorage.removeItem(event.target.id);
                }
                else {
                    event.target.classList.add('tree_favorites');
                    localStorage.setItem(event.target.id, 'favorite');
                };
                //tree_switch ('tree_button_favorites');
            };
        });

    };

    function tree_switch (x) {

        switch(x) {
            case 'tree_button_list':  //Список
                scrollbar_destroy();
                tree_list();
                tree_sort();
                tree_item_classlist();
                scrollbar_create();
                auto_find();
                localStorage.setItem('tree_switch', 'tree_button_list');
                tree_buton_settings ();
                if(edit_button.classList.contains('tree_start_edit')) edit_button_click(); //Убрать настройку дерева, т.к. включен список

                break;
            case 'tree_button_catalog': //Каталог
                scrollbar_destroy();
                tree_catalog();
                tree_catalog_create();
                tree_sort();
                tree_item_classlist();
                scrollbar_create();
                auto_find();
                localStorage.setItem('tree_switch', 'tree_button_catalog');
                tree_buton_settings ();

                break;
            case 'tree_button_favorites': //Избранное
                scrollbar_destroy();
                tree_list();
                tree_favorites();
                tree_sort();
                tree_item_classlist();
                scrollbar_create();
                localStorage.setItem('tree_switch', 'tree_button_favorites')
                if(edit_button.classList.contains('tree_start_edit')) edit_button_click();

                document.getElementById('tree_input_box').style.display = 'none';
                if(!document.getElementById('add_fav_but')) {
                    let div = document.createElement('div');
                    div.id = "add_fav_but";
                    div.classList.add('button_style');
                    div.innerText = 'Добавить/Удалить';
                    document.getElementById('tree_layer').prepend(div);

                    let but_fav = document.getElementById('add_fav_but');
                    but_fav.addEventListener('click', choise_tree_item_window);
                };

                break;
            default:
                console.log("def");
                break;
        };

    };

    auto_find = function() { //Автопоиск

        let items = document.querySelectorAll('.menuitem'); //Выделить все изделия
        items.forEach(item => item.classList.add('hide_item'));//Скрыть все изделия

        let catalog = document.querySelectorAll('.tree_catalog'); //Выделить все каталоги
        catalog.forEach(item => item.classList.add('hide_item'));//Скрыть все каталоги

        items.forEach(function(item, i) {  //Отобразить изделия, удовлетворяющие значению поисковой строки
            if(item.innerText.toString().toLowerCase().includes(tree_input_find.value.toString().toLowerCase())) { //Отображение при совпадении индекса
                item.classList.remove('hide_item');
            };

            if(item.dataset.dogovor) { //Если item.dataset.dogovor существует проверить условие по номеру договора
                if(item.dataset.dogovor.toString().toLowerCase().includes(tree_input_find.value.toString().toLowerCase())) { //Отображение при совпадении номера договора
                    item.classList.remove('hide_item');
                }
            };
        });

        catalog.forEach(function(item, i){  //перечитать каждый каталог
            let podCatalog = item.querySelectorAll('.tree_item'); //найти все строки в каталоге, где стиль не задан
            let hide_item = item.querySelectorAll('.hide_item');
            (podCatalog.length - hide_item.length) == 0 ? item.classList.add('hide_item') : item.classList.remove('hide_item'); // скрыть каталоги, если в них нет изделий или отобразить если есть
            if(tree_input_find.value.toString().toLowerCase() == '') item.classList.remove('hide_item'); //отобразить пустые каталоги, если поиск не задан
        })
    };

    tree.onclick = tree_click; //Запуск слушателя событий открытия/закрытия каталогов
    document.getElementById('tree_edit_layer').style.display = 'none'; //Скрыть ключ настройки дерева

    edit_button.addEventListener('click', edit_button_click);
    create_new_cat.addEventListener('click', create_new_catalog, false);

    tree.addEventListener('click', function(event){ //Событие установки загрузчика выбранного пункта меню
        if(event.target.dataset.izdnomer) menuchoice(event.target.dataset.izdnomer,'0');
        //console.log(event.target.dataset.izdnomer);
    });

    tree_choice_layer = document.getElementById('tree_choice_layer');
    let tree_choice;
    tree_choice_layer.addEventListener('click', function (event) {  //Событие выбора отображения списка
        
        if(event.target.id == 'tree_choice_layer') return;
        let elem = event.target;
        let all_buttons = tree_choice_layer.querySelectorAll('.tree_select');
        all_buttons.forEach(item => {item.classList.remove('tree_menu_active')});
        
        do {
            if(elem.classList.contains('tree_select')) break;
            elem = elem.parentElement;
        } while (elem.id != 'tree_menu')

        elem.classList.add('tree_menu_active');
        tree_choice = elem.id;

        tree_switch (tree_choice);

    });
    
    document.addEventListener('keydown', function () {   //Событие по нажатию ctrl отображает-скрывает ключ для редактирования дерева
        if(event.ctrlKey && localStorage.getItem('tree_switch') == 'tree_button_catalog') {
            document.getElementById('tree_edit_layer').style.display = '';
        };
    }, false);
    document.addEventListener('keyup', function () {
        if(event.key == 'Control' && !document.getElementById('tree_edit').classList.contains('tree_start_edit')) {
            document.getElementById('tree_edit_layer').style.display = 'none';
        };
    }, false);

    tree_input_find.addEventListener('keyup', auto_find); //Автопоиск изделий при вводе в строку поиска
    
    //Загрузка
    localStorage.getItem('tree_switch') == null ? localStorage.setItem('tree_switch', 'tree_button_list') : null;
    tree_switch(localStorage.getItem('tree_switch'));
    tree_switch(localStorage.getItem('tree_switch'));
    document.getElementById(localStorage.getItem('tree_switch')).classList.add('tree_menu_active');

    console.log(document.getElementById('fgewrte'));
</script>

</body>
</html>