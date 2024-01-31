class IzvTableGenerator {

	tableID;
    tabName;
	layerID;
    dbData;
    fieldList;

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
        td.innerText = "Извещения";
        td.colSpan = 999;
        td.style.textAlign = "center";
        td.classList.add("table_colspan_header");
        tr.appendChild(td);
        return tr;
    }

    bottomTitle() {
        let tr = document.createElement("tr");
        let td;
        //console.log(this.dbData[0]);
        let fieldList = this.fieldList.split(", ");
        for(let item in fieldList) {
            td = document.createElement("td");
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
                td.innerHTML = `${value}`;
                td.classList.add("simplefield");
                td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        }

        return tbody;
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
        this.tableID.split("_")[1] != 0 ? addbutton.classList.add("button_field") : addbutton.classList.add("notshow_any");
        addbutton.id = "addButton";
        if(zamok == 0) addbutton.style.display = "none";
        let table = document.getElementById(this.tableID);
        table.after(addbutton);
    }

    createTable(id) {
        //document.getElementById(this.layerID).innerHTML = "";
        let table = document.createElement("table");
		table.id = this.tableID;
        table.classList.add("autotable");
		table.appendChild(this.theadCreate());
        table.appendChild(this.tbodyCreate());
        document.getElementById(this.layerID).append(table);
        //this.createNumberLine();
        //this.createAddButton();
        //this.pages();
        varframe.scrollTop = 9999;
	}
    
}

function izvLoad (izv, tab_id) {
    let data = new FormData();
    data.append(izv, "value");
    data.append("tab_id", tab_id)

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'izveshenie.php', true);
    xhr.send(data);
    xhr.onload = function () {
        let resp = xhr.response; //Результат запроса
        resultArray = JSON.parse(resp);
        console.log(resultArray);

        let table = new IzvTableGenerator();
        table.tableID = `table_${tab_id}`;
        table.layerID = "varframe";
        table.tabName = "Извещения";
        table.fieldList = resultArray[0]['showField'];
        table.dbData = resultArray;

        table.createTable(tab_id);
    }

	xhr.onloadend = function(event) {
        zamok == 1 ? open_edit() : close_edit();
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
    	console.log("(xhrLoad)Загрузка завершена");
  	}
}