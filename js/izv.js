class IzvTableGenerator {

    constructor() {
        if(typeof IzvTableGenerator.instance === 'object') {
            return IzvTableGenerator.instance;
        }

        //this.fieldList = null;
        IzvTableGenerator.instance = this;
    }

	tableID;
    tabName;
	layerID;
    dbData;
    static fieldList;
    showLine;
    midTitle;
    find;

    bigTitle() {
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
        td.id = "midtitle";
        td.innerText = this.midTitle;
        td.colSpan = 999;
        td.style.textAlign = "center";
        td.classList.add("table_colspan_header");
        tr.appendChild(td);
        return tr;
    }

    bottomTitle() {
        let tr = document.createElement("tr");
        let td;
        let fieldList = this.fieldList.split(", ");
        for(let item in fieldList) {
            td = document.createElement("td");
            switch(fieldList[item]) {
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
        let tbody = document.createElement("tbody");
        tbody.classList.add("table_body_block");

        let fieldList = this.fieldList.split(", ");
        for(let i = 1; i < this.dbData.length; i++) {
            let tr = document.createElement("tr");
            
            for(let item in fieldList) {
                if(this.fieldList.indexOf(fieldList[item]) < 0) continue;
                let colName = fieldList[item];
                //let value = this.dbData[i][fieldList[item]];
                let dbLine = this.dbData[i];
                try {
                    tr.appendChild(this.tbodyColRules (colName, dbLine));
                }
                catch {
                    let td = document.createElement("td");
                    td.classList.add("simplefield");
                    td.id = `${dbLine["id"]}_${colName}_${this.dbData[0]["db"]}`;
                    td.innerHTML = `${dbLine[colName]}`;
                    tr.appendChild(td);
                }
            }
            tbody.appendChild(tr);
        }
        return tbody;
    }

    tbodyColRules (colName, dbLine) {
        //console.log("tbodyColRules");
        let td = document.createElement("td");
        let itemID = `${dbLine["id"]}_${colName}_${this.dbData[0]["db"]}`;
        let value = `${dbLine[colName]}`;
        switch (colName) {
            case "numii":
                td.style.minWidth = 100 + "px";
                td.classList.add("simplefield");
                td.id = itemID;
                td.innerHTML = `${value}`;
                break;
            case "scan":
                td.id = itemID;
                break;
            case "trudoemc":
                td.id = itemID;
                break;
            case "date":
                td.id = itemID;
                let input = document.createElement("input");
                input.id = `${itemID}_i`;
                input.classList.add("dateinput");
                input.setAttribute("type", "text");
                input.setAttribute("onchange", "update_db(this.id,this.value)");
                input.setAttribute("value", `${value}`);
                td.appendChild(input);
                break;
            case "izdname":
                td.classList.add("table_item_header");
                td.innerHTML = `${value}`;
                td.id = itemID;
                break;
            default:
                td.classList.add("simplefield");
                td.id = itemID;
                td.innerHTML = `${value}`;
                break;
        }
        //return null;
        return td;
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
            td.dataset.actfile = "izveshenie.php";
            td.dataset.htag = this.layerID;
            td.dataset.getdata = `&id=${this.tableID.split("_")[1]}`;
            document.querySelectorAll(`#${this.tableID} tbody tr`)[i].children[0].before(td);
        }
    }

    pages() {
        let pages = document.createElement("div");
        pages.classList.add("mailbox_pages");
        pages.id = "pages";

        if((this.dbData[0]["maxResult"]/this.showLine|0) > 0) {     
            for(let i = 0; i < (this.dbData[0]["maxResult"]/this.showLine|0) + 1; i++) {
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
        (this.tableID.split("_")[1] != 0 && this.tableID.split("_")[1] != "-1") ? addbutton.classList.add("button_field") : addbutton.classList.add("notshow_any");
        addbutton.id = "addButton";
        if(zamok == 0) addbutton.style.display = "none";
        let table = document.getElementById(this.tableID);
        table.after(addbutton);
    }

    createEvents() {
        let table = document.getElementById(this.tableID);
        let find = this.find;
		table.addEventListener("click", function(event) {
            let type = "";
            if(event.target.classList == "button_field"){
                if(event.target.parentNode.id.split("_")[1] == "scan") type = 2;
                if(event.target.parentNode.id.split("_")[1] == "trudoemc") type = 1;
                okno_show('dialog',`&tabname=docwork&type=${type}&id=${event.target.parentNode.id.split("_")[0]}`);
            }
        })

        let pages = document.getElementById("pages");
        let tableID = this.tableID;
        let tablediv = document.getElementById(this.layerID);
        if(pages != null) {
            pages.addEventListener("click", function(event) {
                //console.log(event.target.classList.contains("menuitem"));
                if(event.target.classList.contains("menuitem")) {
                    tablediv.innerHTML = "";
                    //xhrLoad("xhrload", tb.id.split("_")[1], event.target.innerText, findline.value);
                    let sendObject = {
                        "tab_id": tableID.split("_")[1], 
                        "page": event.target.innerText, 
                        "find": find
                    }
                    izvLoad(sendObject);
                }
            })
        }

        
        let addButton = document.getElementById("addButton");
        addButton.addEventListener("click", function(event) {

            let data = new FormData();
            data.append("add", tableID.split("_")[1]);
        
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'izveshenie.php', true);
            xhr.send(data);
            xhr.onload = function () {
                let id = xhr.response; //Результат запроса
                //resultArray = JSON.parse(resp);
                //console.log("id: " + id);

                tablediv.innerHTML = "";
                let sendObject = {
                    "tab_id": tableID.split("_")[1], 
                    "page": 0, 
                    "find": find
                }
                izvLoad(sendObject);
                //xhrLoad("xhrload", tableID.split("_")[1], 0, findline.value);
            }
        })
         
        let colSort = document.querySelector("[data-column='numii']");
        if(localStorage.getItem('sort_col') == "1") {
            colSort.classList.add("sortColumn");
        }
        if(localStorage.getItem('sort_col') == null) {
            colSort.classList.add("unSortColumn");
        }
        colSort.addEventListener("click", function(event) {
            colSort.classList.toggle("sortColumn");
            colSort.classList.toggle("unSortColumn");
            if(colSort.classList.contains("sortColumn")) {
                localStorage.setItem('sort_col', "1");
            }
            else {
                localStorage.removeItem('sort_col');
            }

            let sendObject = {
                "tab_id": tableID.split("_")[1], 
                "page": 0, 
                "find": find
            }
            izvLoad(sendObject);
        })

    }

    loadAllFileIcon() {
        //console.log("loadAllFileIcon");
        let td = document.querySelectorAll('[id$="_scan_docwork"]');
        let items = [];
        td.forEach(item => {
            item.innerHTML = "";
            items.push(item.id.split("_")[0]);
        });
    
        let data = new FormData();
        data.append("finditems", items);
    
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'izveshenie.php', true);
        xhr.send(data);
        xhr.onload = function () {
            let resp = xhr.response; //Результат запроса
            resultArray = JSON.parse(resp);
            createIcons(resultArray, [['2', '_scan_docwork'], ['1', '_trudoemc_docwork']]);
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
        this.createAddButton();
        this.loadAllFileIcon();
        this.pages();
        this.createEvents();
        tablediv.scrollTop = 9999;
	}
    
}

function izvLoad (sendObject) {
    let data = new FormData();
    data.append("izv", "value");
    data.append("showLine", 100);
    data.append("sort_col", localStorage.getItem('sort_col'));
    for(let key in sendObject) data.append(key, sendObject[key]);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'izveshenie.php', true);
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

        let table = new IzvTableGenerator();
        table.tableID = `table_${sendObject.tab_id}`;
        table.layerID = tableLayerName;
        table.find = sendObject.find;
        table.tabName = "Извещения";
        if(sendObject.tab_id != -1) table.midTitle = document.getElementById(`${sendObject.tab_id}_name_izdelie`).innerHTML.replace(/<br>/g, '');
        if(sendObject.tab_id == -1) table.midTitle = "Найти";
        table.showLine = 100;
        table.fieldList = resultArray[0]['showField'];
        table.dbData = resultArray;

        table.createTable();
    }

	xhr.onloadend = function(event) {
        zamok == 1 ? open_edit() : close_edit();
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
    	console.log("(izvLoad)Загрузка завершена");
        if(sendObject.find != "") findSelect(sendObject.find);
  	}
}

function izvfindbox(tabid) {
    if(document.getElementById("findlayer")) document.getElementById("findlayer").remove();
    let div = document.createElement("div");
    div.id = "findlayer";
    let textNode = document.createTextNode("");
    textNode.data = "Найти: ";
    let input = document.createElement("input");
    input.style.width = 600 + 'px';
    input.id = "findline";
    div.appendChild(textNode);
    div.appendChild(input);

    let varframe = document.getElementById("varframe");
    varframe.appendChild(div);

    
    let findline = document.getElementById("findline");
    findline.addEventListener("change", function(event) {
            let sendObject = {
                "tab_id": tabid, 
                "page": 0, 
                "find": findline.value
            }
            izvLoad(sendObject);
            //console.log(`tabid: ${tabid}; findline.value: ${findline.value}`);
    });
}