class MailTableGenerator {

    constructor() {
        if(typeof MailTableGenerator.instance === 'object') {
            return MailTableGenerator.instance;
        }

        //this.fieldList = null;
        MailTableGenerator.instance = this;
    }

	tableID;
    tabName;
	layerID;
    dbData;
    static fieldList;
    find;

    bigTitle() {
        // console.log("----Mail data-----");
        // console.log("tableID: " + this.tableID);
        // console.log("tabName: " + this.tabName);
        // console.log("layerID: " + this.layerID);
        // console.log("dbData: " + this.dbData);
        // console.log("fieldList: " + this.fieldList);
        // console.log("find: " + this.find);
        // console.log("--------");
        let tabName = !this.tabName ? "Таблица" : this.tabName;
        let tr = document.createElement("tr")
        let td = document.createElement("td");
        td.innerText = tabName;
        td.colSpan = 999;
        td.style.textAlign = "center";
        td.classList.add("table_big_header");
        tr.appendChild(td);
        return tr;
    }

    middleTitle() {
        let tr = document.createElement("tr");
        let td = document.createElement("td");
        td.id = "inbox";
        td.innerText = "Входящие";
        td.colSpan = 1;
        td.style.textAlign = "center";
        td.classList.add("table_colspan_header");
        tr.appendChild(td);

        td = document.createElement("td");
        td.id = "outbox";
        td.innerText = "Исходящие";
        td.style.borderLeftWidth = '2px';
        td.colSpan = 999;
        td.style.textAlign = "center";
        td.classList.add("table_colspan_header");
        tr.appendChild(td);
        return tr;
    }

    createMiddleSize() {
        //let size = document.querySelector('[data-column="dateish"]').cellIndex;
        //document.getElementById("inbox").colSpan = size;
        //console.log(size);
    }


    bottomTitle() {
        let tr = document.createElement("tr");
        let td;
        //console.log(this.dbData[0]);
        let fieldList = this.fieldList.split(", ");
        for(let item in fieldList) {
            td = document.createElement("td");
            switch(fieldList[item]) {
                case "dateish":
                    td.style.borderLeftWidth = '2px';
                case "datevh":
                    td.style.width = "70px";
                break;
                case "scanvh":
                    td.style.minWidth = "45px";
                break;
                case "sumnormchasvh":
                    td.style.width = "50px";
                break;
                case "scanish":
                    td.style.minWidth = "45px";
                break;
                case "sumnormchasish":
                    td.style.width = "50px";
                break;
                case "izdname":
                    td.style.minWidth = "140px";
                break;
            }
            td.innerHTML = `${this.dbData[0][fieldList[item]]}`;
            td.classList.add("table_header");
            td.classList.add("bottomTitle");
            td.dataset.column = fieldList[item];
            tr.appendChild(td);
        }

        tr.appendChild(td);
        return tr;
    }

    theadCreate() {
        let thead = document.createElement("thead");
        thead.classList.add("table_header_block");
        thead.id = "table_header_block";
        thead.appendChild(this.bigTitle());
        thead.appendChild(this.middleTitle());
        thead.appendChild(this.bottomTitle());
        return thead;
    }

    tbodyCreate() {
        let tr, td, input, value;
        let tbody = document.createElement("tbody");
        tbody.classList.add("table_body_block");

        let fieldList = this.fieldList.split(", ");
        for(let i = 1; i < this.dbData.length; i++) {
            tr = document.createElement("tr");
            
            for(let item in fieldList) {
                if(this.fieldList.indexOf(fieldList[item]) < 0) continue;
                value = this.dbData[i][fieldList[item]];
                td = document.createElement("td");

                switch (fieldList[item]) {
                    case "dateish":
                        td.style.borderLeftWidth = '2px';
                    case "datevh":
                    case "datereg":
                    case "datecontrol":
                        input = document.createElement("input");
                        input.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}_i`;
                        input.classList.add("dateinput");
                        input.setAttribute("type", "text");
                        input.setAttribute("onchange", "update_db(this.id,this.value);dateColor()");
                        input.setAttribute("value", `${value}`);
                        td.appendChild(input);
                    case "scanvh":
                    case "scanish":
                        td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                        //let img = document.createElement("img");
                        //img.src = `include/mini-loading.gif`;
                        //img.style.float = "left";
                        //td.appendChild(img);
                        break;
                    case "izdname":
                        td.classList.add("table_item_header");
                        td.innerHTML = `${value}`;
                        td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                        break;
                    case "sumnormchasvh":
                        td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                        let equvh = this.dbData[i]['countlistvh'];
                        try {
                            if(equvh) td.innerHTML = equationCreate(equvh) * 0.5;
                        }
                        catch {
                            td.innerHTML = "-"
                        }
                        break;
                    case "sumnormchasish":
                        td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                        let equish = this.dbData[i]['countlistish'];
                        try {
                            if(equish) td.innerHTML = equationCreate(equish) * 5;
                        }
                        catch {
                            td.innerHTML = "-"
                        }
                        break;
                    
                    default:
                        td.innerHTML = `${value}`;
                        td.classList.add("simplefield");
                        td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                        break;
                }

                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        }

        return tbody;
    }

    addColorStyle() {
        let items = document.querySelectorAll('[id$="_datereg_mailbox"], [id$="_nomerreg_mailbox"]');
        items.forEach(item => item.classList.add("regstyle"));
    }

    createNumberLine() {
        let title = document.querySelectorAll(`#${this.tableID} .table_header`);
        let td = document.createElement("td");
        title[0].before(td);
        td.innerText = "№";
        td.classList.add("table_header");
        td.classList.add("bottomTitle");
        td.style.width = "25px";

        let len = document.querySelectorAll(`#${this.tableID} tbody tr`).length;
        for(let i = 0; i < len; i++) {
            td = document.createElement("td");
            td.innerText = i + 1;
            td.classList.add("table_nomeric_col");
            td.classList.add("relocnomer");
            td.dataset.id = document.querySelectorAll(`#${this.tableID} tbody tr`)[i].children[1].id.split("_")[0];
            td.dataset.table = document.querySelectorAll(`#${this.tableID} tbody tr`)[i].children[1].id.split("_")[2];
            td.dataset.actfile = "mailbox.php";
            td.dataset.htag = this.layerID;
            td.dataset.getdata = `&id=${this.tableID.split("_")[1]}`;
            document.querySelectorAll(`#${this.tableID} tbody tr`)[i].children[0].before(td);
        }
    }

    pages() {
        let pages = document.createElement("div");
        pages.classList.add("mailbox_pages");
        pages.id = "pages";

        if((this.dbData[0]["maxResult"]/100|0) > 0) {     
            for(let i = 0; i < (this.dbData[0]["maxResult"]/100|0) + 1; i++) {
                let page = document.createElement("div");
                page.innerText = i + 1;
                page.classList.add("menuitem");
                if((i + 1) == this.dbData[0]["page"]) page.classList.add("menuactive");
                pages.appendChild(page);
            }
        }
        document.getElementById(this.layerID).append(pages);
    }

    createAddButton() {
        let addbutton = document.createElement("img");
        addbutton.src = `include/addline.png`;
        //addbutton.classList.add("button_field");
        (this.tableID.split("_")[1] != 0 && this.tableID.split("_")[1] != "-1") ? addbutton.classList.add("button_field") : addbutton.classList.add("notshow_any");
        addbutton.id = "addButton";
        if(zamok == 0) addbutton.style.display = "none";
        let table = document.getElementById(this.tableID);
        table.after(addbutton);
    }

    createEvents() {
        let table = document.getElementById(this.tableID);
		table.addEventListener("click", function(event) {
            let type = "";
            if(event.target.classList == "button_field"){
                if(event.target.parentNode.id.split("_")[1] == "scanvh") type = 1;
                if(event.target.parentNode.id.split("_")[1] == "scanish") type = 2;
                okno_show('dialog',`&tabname=mailbox&type=${type}&id=${event.target.parentNode.id.split("_")[0]}`);
            }
        });

        table.addEventListener("focusout", function(event) {
            let item = event.target.innerHTML;
            let result = event.target.nextElementSibling;
            if(event.target.id.split("_")[1] == "countlistvh") {
                try {
                    result.innerHTML = equationCreate(item.replace(/\<br\>/g,' ')) * 0.5;
                }
                catch {
                    result.innerHTML = "-"
                }
            }
            if(event.target.id.split("_")[1] == "countlistish") {
                try {
                    result.innerHTML = equationCreate(item.replace(/\<br\>/g,' ')) * 5;
                }
                catch {
                    result.innerHTML = "-"
                }
            }
        })

        let sendObject = {};
        let find = this.find;
        let pages = document.getElementById("pages");
        let tb = document.getElementById(this.tableID);
        let tablediv = document.getElementById(this.layerID);
        if(pages != null) {
            pages.addEventListener("click", function(event) {
                //console.log(event.target.classList.contains("menuitem"));
                if(event.target.classList.contains("menuitem")) {
                    tablediv.innerHTML = "";
                    
                    let sendObject = {
                        "tabNumber": tb.id.split("_")[1],
                        "page": event.target.innerText,
                        "find": find
                    };
                    xhrLoad(sendObject);
                }
            });
        }

        let tableID = this.tableID;
        let addButton = document.getElementById("addButton");
        addButton.addEventListener("click", function(event) {

            let data = new FormData();
            data.append("add", tableID.split("_")[1]);
        
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'mailbox.php', true);
            xhr.send(data);
            xhr.onload = function () {
                let id = xhr.response; //Результат запроса
                //resultArray = JSON.parse(resp);
                //console.log("id: " + id);

                tablediv.innerHTML = "";
                let sendObject = {
                    "tabNumber": tableID.split("_")[1],
                    "page": 0,
                    "find": find
                };
                xhrLoad(sendObject);
            }
        });
    }

    loadAllFileIcon() {
        //console.log("loadAllFileIcon");
        let td = document.querySelectorAll('[id$="_scanvh_mailbox"]');
        let items = [];
        td.forEach(item => {
            item.innerHTML = "";
            items.push(item.id.split("_")[0]);
        });
    
        let data = new FormData();
        data.append("finditems", items);
    
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'mailbox.php', true);
        xhr.send(data);
        xhr.onload = function () {
            let resp = xhr.response; //Результат запроса
            resultArray = JSON.parse(resp);
            createIcons(resultArray, [['1', '_scanvh_mailbox'], ['2','_scanish_mailbox']]);
        }
    }

	createTable() {
        document.getElementById(this.layerID).innerHTML = "";
        let table = document.createElement("table");
		table.id = this.tableID;
        table.classList.add("autotable");
		table.appendChild(this.theadCreate());
        table.appendChild(this.tbodyCreate());
        document.getElementById(this.layerID).append(table);
        this.createNumberLine();
        this.createMiddleSize();
        this.addColorStyle();
        this.createAddButton();
        this.pages();
        this.createEvents();
        this.loadAllFileIcon();
        createIconPanel();
        createHideShowColumnButton("datereg", "button_data.png");
        createHideShowColumnButton("nomerreg", "button_nomer.png");
        createHideShowColumnButton("prim", "button_prim.png");
        
        dateColor();
        //let varframe = document.getElementById(varframe);
        //varframe.scrollTop = tablediv.scrollHeight;
  
        tablediv.scrollTop = 9999;
	}

}

function colHide (head, hide, callback) {
    //let head = document.querySelector(`[data-column="${head1}"]`);
    let allrows = head;
    let rowNm = head;
    while (allrows.tagName != "TABLE") allrows = allrows.parentNode;
    while (rowNm.tagName != "TR") rowNm = rowNm.parentNode;
    let tgtcol = document.querySelectorAll(`td:nth-child(${head.cellIndex + 1})`);
    tgtcol.forEach(item => {
        let row = item;
        while (row.tagName != "TR") row = row.parentNode;
        if(row.rowIndex >= rowNm.rowIndex && hide == 1) item.style.display = "none";
        if(row.rowIndex >= rowNm.rowIndex && hide == 0) item.style.display = "";
    })
    callback();
}

function titleResize() {
    let dateishIndex = document.querySelector('[data-column="dateish"]').cellIndex;
    let size = document.querySelectorAll(`.bottomTitle:nth-child(-n+${dateishIndex}):not([style*="display: none"])`).length;
    document.getElementById("inbox").colSpan = size;
    return size;
}

function createIconPanel() {
    let inbox = document.getElementById(`inbox`);
    let div = document.createElement(`div`);
    div.id = `iconPanel`;
    inbox.appendChild(div);

    let columHideEvent = document.getElementById("table_header_block");
    columHideEvent.addEventListener("click", function(e) {
        
        if(e.target.dataset.colrule) {
            let col = e.target.dataset.column;
            //console.log(e.target);
            colHide (document.querySelector(`[data-column="${col}"]`), 1, titleResize);
            document.getElementById(`${col}_div`).style.display = "";
            localStorage.setItem(`${col}Col`, "1");
            localStorage.setItem(`${col}Button`, "0");
        }

        if(e.target.dataset.colname) {
            let col = e.target.dataset.colname;
            colHide (document.querySelector(`[data-column="${col}"]`), 0, titleResize);
            document.getElementById(`${col}_div`).style.display = "none";
            localStorage.setItem(`${col}Col`, "0");
            localStorage.setItem(`${col}Button`, "1");
        }
    });
}

function createHideShowColumnButton(name, image) {
    let iconPanel = document.getElementById("iconPanel");
    let button_div = document.createElement(`div`);
    let img = document.createElement(`img`);
    button_div.id = `${name}_div`;
    img.src = `include/${image}`;
    img.id = `${name}_img`;
    img.dataset.colname = name;
    button_div.appendChild(img);
    iconPanel.appendChild(button_div);
    document.querySelector(`[data-column="${name}"]`).dataset.colrule = name;

    if(!localStorage.getItem(`${name}Col`)) {
        localStorage.setItem(`${name}Col`, `1`);
        localStorage.setItem(`${name}Button`, `0`);
        colHide (document.querySelector(`[data-column="${name}"]`), 1, titleResize);
    };
    if(localStorage.getItem(`${name}Col`) == 1) colHide (document.querySelector(`[data-column="${name}"]`), 1, titleResize);
    if(localStorage.getItem(`${name}Col`) == 0) colHide (document.querySelector(`[data-column="${name}"]`), 0, titleResize);
    if(localStorage.getItem(`${name}Button`) == 1) document.getElementById(`${name}_div`).style.display = `none`;
    if(localStorage.getItem(`${name}Button`) == 0) document.getElementById(`${name}_div`).style.display = ``;
}

function xhrLoad (sendObject) {
    let data = new FormData();
    data.append("xhrload", "xhrload");
    for(let key in sendObject) data.append(key, sendObject[key]);
    
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'mailbox.php', true);
    xhr.send(data);
    xhr.onload = function () {
        let resp = xhr.response; //Результат запроса
        resultArray = JSON.parse(resp);

        let baseLayer = !sendObject.baseLayer ? "varframe" : sendObject.baseLayer;
        let tableLayerName = !sendObject.tableLayerName ? "tablediv" : sendObject.tableLayerName;

        if(!document.getElementById(tableLayerName)) {
            let tablayer = document.createElement("div");
            tablayer.id = tableLayerName;
            varframe = document.getElementById(baseLayer);
            varframe.appendChild(tablayer);
        }

        let table = new MailTableGenerator();
        table.tableID = `table_${sendObject.tabNumber}`;
        table.layerID = tableLayerName;
        table.find = sendObject.find;
        // console.log("sendObject.fieldList: " + sendObject.fieldList);
        // console.log("table.fieldList: " + table.fieldList);
        if(typeof sendObject.fieldList !== "undefined") table.fieldList = sendObject.fieldList;
        table.tabName = "Переписка";
        if(sendObject.tabNumber == 0 || sendObject.tabNumber == -1) table.fieldList = "izdname, " + table.fieldList;
        table.dbData = resultArray;

        table.createTable();
        //callback();
    }

	xhr.onloadend = function(event) {

        zamok == 1 ? open_edit() : close_edit();
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
    	console.log("(xhrLoad)Загрузка завершена");
    	//console.log("sendObject.find: " + sendObject.find);
        if(sendObject.find != "") findSelect(sendObject.find);
		//loadAllFileIcon();
  	}
}



function mailfindbox(tabid) {
    if(document.getElementById("findlayer")) document.getElementById("findlayer").remove();

    let div = document.createElement("div");
    div.id = "findlayer";

    let textNode = document.createTextNode("");
    textNode.data = "Найти:";

    let input = document.createElement("input");
    input.style.width = 600 + 'px';
    input.id = "findline";

    let img = document.createElement("img");
    img.src = `include/question.png`;
    let div2 = document.createElement("div");
    div2.classList.add("mailQuestion");
    div2.style.marginLeft = 5 + 'px';
    div2.dataset.title = "Поиск по диапазону дат:\n- указать диапазон между знаками # #;\n\
- после можно указать поисковое слово.\n\nНапример:\
\n#10.01.2022-16.03.2022#\nБудут выведены все строки между указанными \nдатами включая 10 и 16 число.\n\
\n#10.01.2022-16.03.2022# изделие\n\
В указанном диапазоне будет задан поиск по слову 'изделие'.";


    div.appendChild(textNode);
    div.appendChild(input);
    div.appendChild(div2);
    div2.appendChild(img);

    let varframe = document.getElementById("varframe");
    varframe.appendChild(div);

    
    let findline = document.getElementById("findline");
    findline.addEventListener("change", function(event) {
        let sendObject = {
            "tabNumber": tabid,
            "page": 0,
            "find": findline.value
        };
        xhrLoad(sendObject);
    });
}

function dateColor() {
    let dt = new Date();
    let controlCol = document.querySelectorAll(`[id$="_datecontrol_mailbox_i"]`);
    controlCol.forEach(item => {
        if(item.value != "") {
            let dateIsh = document.getElementById(item.id.split("_")[0] + "_dateish_mailbox").firstChild.value;
            dateIsh != "" ? dateIsh = dateIsh.split(".")[2] + dateIsh.split(".")[1] + dateIsh.split(".")[0] : null;
            let dateCtrl = item.value.split(".")[2] + item.value.split(".")[1] + item.value.split(".")[0];
            let today = `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;

            if(parseInt(dateCtrl) <= parseInt(today) && dateIsh == "") item.parentNode.style.backgroundColor = "#eddddd"; //red
            if(parseInt(dateCtrl) > parseInt(today) && dateIsh == "") item.parentNode.style.backgroundColor = "#ceedce"; //green
            if(dateCtrl != "" && dateIsh != "") item.parentNode.style.backgroundColor = "";
        }
    })
}

function equationCreate(data) {
    return new Function(`return ${data}`)();
}