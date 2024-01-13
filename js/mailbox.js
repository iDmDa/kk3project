class TableGenerator {

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
        td.innerText = "Входящие";
        td.colSpan = 8;
        td.style.textAlign = "center";
        td.classList.add("table_colspan_header");
        tr.appendChild(td);

        td = document.createElement("td");
        td.innerText = "Исходящие";
        td.colSpan = 999;
        td.style.textAlign = "center";
        td.classList.add("table_colspan_header");
        tr.appendChild(td);
        return tr;
    }


    bottomTitle() {
        let tr = document.createElement("tr");
        let td;
        let fieldList = this.fieldList.split(", ")
        for(let item in fieldList) {
            td = document.createElement("td");
            switch(fieldList[item]) {
                case "scanvh":
                    td.style.width = "60px";
                break;
                case "sumnormchasvh":
                    td.style.width = "50px";
                break;
                case "scanish":
                    td.style.width = "60px";
                break;
                case "sumnormchasish":
                    td.style.width = "50px";
                break;
            }
            td.innerHTML = `${this.dbData[0][fieldList[item]]}`;
            td.classList.add("table_header");
            td.dataset.column = fieldList[item];
            tr.appendChild(td);
        }

        tr.appendChild(td);
        return tr;
    }

    theadCreate() {
        let thead = document.createElement("thead");
        thead.classList.add("table_header_block");
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
                    case "datevh":
                    case "dateish":
                        input = document.createElement("input");
                        input.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                        input.classList.add("dateinput");
                        input.setAttribute("type", "text");
                        input.setAttribute("onchange", "update_db(this.id,this.value)");
                        input.setAttribute("value", `${value}`);
                        td.appendChild(input);
                        break;

                    case "scanvh":
                    case "scanish":
                        td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
                        let img = document.createElement("img");
                        img.src = `include/mini-loading.gif`;
                        img.style.float = "left";
                        td.appendChild(img);
                        //td.innerText = 1;
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


    createNumberLine() {
        let title = document.querySelectorAll(`#${this.tableID} .table_header`);
        let td = document.createElement("td");
        title[0].before(td);
        td.innerText = "№";
        td.classList.add("table_header");
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
            td.dataset.htag = "varframe";
            td.dataset.getdata = `&id=${this.tableID.split("_")[1]}`;
            document.querySelectorAll(`#${this.tableID} tbody tr`)[i].children[0].before(td);
        }
    }

	createTable() {
		let table = document.createElement("table");
		table.id = this.tableID;
        table.classList.add("autotable");
		table.appendChild(this.theadCreate());
        table.appendChild(this.tbodyCreate());
        document.getElementById(this.layerID).append(table);
        this.createNumberLine();

        let tb = document.getElementById(this.tableID);
		tb.addEventListener("click", function(event) {
            let type = "";
            if(event.target.classList == "button_field"){
                console.log(event.target.classList);
                console.log(event.target.parentNode.id.split("_")[1]);
                console.log(event.target);
                if(event.target.parentNode.id.split("_")[1] == "scanvh") type = 1;
                if(event.target.parentNode.id.split("_")[1] == "scanish") type = 2;
                okno_show('dialog',`&tabname=mailbox&type=${type}&id=${event.target.parentNode.id.split("_")[0]}`);
            }
        });
	}

}

function xhrLoad (postname, value, tabNumber) {
    let data = new FormData();
    data.append(postname, value);
	data.append("tabNumber", tabNumber);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'mailbox.php', true);
    xhr.send(data);
    xhr.onload = function () {
        let resp = xhr.response; //Результат запроса
        resultArray = JSON.parse(resp);

		let table = new TableGenerator();
		console.log(tabNumber);
		table.tableID = `table_${tabNumber}`;
		table.layerID = "varframe";
		table.tabName = "Переписка";
		table.fieldList = "datevh, nomervh, adresvh, contentvh, scanvh, countlistvh, sumnormchasvh, dateish, nomerish, adresish, contentish, scanish, countlistish, sumnormchasish, fioispish";
		table.dbData = resultArray;

		table.createTable();

		zamok == 1 ? open_edit() : close_edit();
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });

        //callback();
    }

	xhr.onloadend = function(event) {
    	console.log("Загрузка завершена");
		loadFileIcon();
  	}
}

function loadFileIcon() {
    let td = document.querySelectorAll('[id$="_scanvh_mailbox"], [id$="_scanish_mailbox"]');
    td.forEach(item => {
        itemLoad(item);
    });
}

function createLoadButton(td) {
    let img = document.createElement("img");
    img.src = `include/new window.png`;
    img.style.float = "right";
    img.classList.add("button_field");
    if(zamok == 0) img.style.display = "none";
    td.appendChild(img);
}

function itemLoad(td) {
    let scan_id = td.id.split("_")[0];
    let scan_type = td.id.split("_")[1] == "scanvh" ? 1 : 2;
    let data = new FormData();
    data.append("scanload", "1");
	data.append("scan_id", scan_id);
    data.append("scan_type", scan_type);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'mailbox.php', true);
    xhr.send(data);
    xhr.onload = function () {
        let resp = xhr.response; //Результат запроса
        resultArray = JSON.parse(resp);
        console.log(resultArray);
        createIcon(resultArray, td);
    }
}

function createIcon(filelist, td) {
    td.innerHTML = "";
    if(filelist.length > 0) {
        let extList = "bmp, doc, docx, gif, jpg, mp3, pdf, png, tif, tiff, txt, xls, xlsx";
        for(let j = 0; j < filelist.length; j++) {
            let filetype = filelist[j]["filename"].split(".").reverse()[0].toLowerCase();
            let a = document.createElement("a");
            let img = document.createElement("img");
            let textNode = document.createTextNode(" ");
            a.href = `/projectdata/mailbox/${filelist[j]["prefix"]}_${filelist[j]["filename"]}`;
            a.target = "_blank";
            extList.indexOf(filetype) < 0 ? img.src = `include/ico/unknow.png` : img.src = `include/ico/${filetype}.png`;
            img.title = `${filelist[j]["filename"]}`;
            a.appendChild(img);
            td.appendChild(a);
            if(j != filelist.length - 1) td.appendChild(textNode);
        }
    }
    createLoadButton(td);
}