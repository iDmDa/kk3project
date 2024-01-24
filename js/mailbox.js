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
        console.log(this.dbData[0]);
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
                    case "sumnormchasvh":
                    case "sumnormchasish":
                        td.innerHTML = `${value}`;
                        td.id = `${this.dbData[i]["id"]}_${fieldList[item]}_${this.dbData[0]["db"]}`;
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
        addbutton.classList.add("button_field");
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

        let pages = document.getElementById("pages");
        let tb = document.getElementById(this.tableID);
        let tablediv = document.getElementById(this.layerID);
        if(pages != null) {
            pages.addEventListener("click", function(event) {
                //console.log(event.target.classList.contains("menuitem"));
                if(event.target.classList.contains("menuitem")) {
                    tablediv.innerHTML = "";
                    xhrLoad("xhrload", tb.id.split("_")[1], event.target.innerText, "", findline.value);
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
                console.log("id: " + id);

                tablediv.innerHTML = "";
                xhrLoad("xhrload", tableID.split("_")[1], 0, id, findline.value);
            }
        });



    }



    loadAllFileIcon() {
        console.log("loadAllFileIcon");
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
            createIcons(resultArray);
        }
    }

	createTable(id) {
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
        dateColor();
        //let varframe = document.getElementById(varframe);
        //varframe.scrollTop = tablediv.scrollHeight;
  
        varframe.scrollTop = 9999;
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
    let inbox = document.getElementById("inbox");
    let div = document.createElement("div");
    div.id = "iconPanel";

    let regdata_div = document.createElement("div");
    let regdata_img = document.createElement("img");
    regdata_div.id = "regdata_div";
    regdata_img.src = `include/regdata.png`;
    regdata_img.id = "regdata_img";

    let nomerreg_div = document.createElement("div");
    let nomerreg_img = document.createElement("img");
    nomerreg_div.id = "nomerreg_div";
    nomerreg_img.src = `include/regno.png`;
    nomerreg_img.id = "nomerreg_img";

    regdata_div.appendChild(regdata_img);
    nomerreg_div.appendChild(nomerreg_img);

    div.appendChild(regdata_div);
    div.appendChild(nomerreg_div);
    inbox.appendChild(div);

    //console.log(localStorage.getItem("datereg"));
    if(!localStorage.getItem("dateregCol")) {
        localStorage.setItem("dateregCol", "1");
        localStorage.setItem("dateregButton", "0");
        colHide (document.querySelector(`[data-column="datereg"]`), 1, titleResize);
    };
    if(!localStorage.getItem("nomerregCol")) {
        localStorage.setItem("nomerregCol", "1");
        localStorage.setItem("nomerregButton", "0");
        colHide (document.querySelector(`[data-column="nomerreg"]`), 1, titleResize);
    };

    if(localStorage.getItem("dateregCol") == 1) colHide (document.querySelector(`[data-column="datereg"]`), 1, titleResize);
    if(localStorage.getItem("dateregCol") == 0) colHide (document.querySelector(`[data-column="datereg"]`), 0, titleResize);
    if(localStorage.getItem("dateregButton") == 1) document.getElementById("regdata_div").style.display = "none";
    if(localStorage.getItem("dateregButton") == 0) document.getElementById("regdata_div").style.display = "";

    if(localStorage.getItem("nomerregCol") == 1) colHide (document.querySelector(`[data-column="nomerreg"]`), 1, titleResize);
    if(localStorage.getItem("nomerregCol") == 0) colHide (document.querySelector(`[data-column="nomerreg"]`), 0, titleResize);
    if(localStorage.getItem("nomerregButton") == 1) document.getElementById("nomerreg_div").style.display = "none";
    if(localStorage.getItem("nomerregButton") == 0) document.getElementById("nomerreg_div").style.display = "";

    let columHideEvent = document.getElementById("table_header_block");

    columHideEvent.addEventListener("click", function(e) {
        console.log(e.target);
        switch (e.target.dataset.column) {
            case "datereg":
                colHide (document.querySelector(`[data-column="datereg"]`), 1, titleResize);
                document.getElementById("regdata_div").style.display = "";
                localStorage.setItem("dateregCol", "1");
                localStorage.setItem("dateregButton", "0");
                break;
            case "nomerreg":
                colHide (document.querySelector(`[data-column="nomerreg"]`), 1, titleResize);
                document.getElementById("nomerreg_div").style.display = "";
                localStorage.setItem("nomerregCol", "1");
                localStorage.setItem("nomerregButton", "0");
                break;
        }
        switch (e.target.id) {
            case "regdata_img":
                colHide (document.querySelector(`[data-column="datereg"]`), 0, titleResize);
                document.getElementById("regdata_div").style.display = "none";
                localStorage.setItem("dateregCol", "0");
                localStorage.setItem("dateregButton", "1");
                break;
            case "nomerreg_img":
                colHide (document.querySelector(`[data-column="nomerreg"]`), 0, titleResize);
                document.getElementById("nomerreg_div").style.display = "none";
                localStorage.setItem("nomerregCol", "0");
                localStorage.setItem("nomerregButton", "1");
                break;       
            default:
                break;
        }
    });
    

}



function xhrLoad (postname, tabNumber, page, id, find) {
    let data = new FormData();
    data.append(postname, "value");
	data.append("tabNumber", tabNumber);
    data.append("page", page);
    data.append("find", find);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'mailbox.php', true);
    xhr.send(data);
    xhr.onload = function () {
        let resp = xhr.response; //Результат запроса
        resultArray = JSON.parse(resp);
        //console.log(resultArray);

        let table = new TableGenerator();
        //console.log(tabNumber);
        table.tableID = `table_${tabNumber}`;
        table.layerID = "tablediv";
        table.tabName = "Переписка";
        table.fieldList = "datevh, nomervh, adresvh, contentvh, scanvh, countlistvh, sumnormchasvh, datereg, nomerreg, datecontrol, dateish, nomerish, adresish, contentish, scanish, countlistish, sumnormchasish, fioispish";
        if(tabNumber == 0) table.fieldList = "izdname, " + table.fieldList;
        table.dbData = resultArray;

        table.createTable(id);
        //callback();
    }

	xhr.onloadend = function(event) {

        zamok == 1 ? open_edit() : close_edit();
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
    	console.log("(xhrLoad)Загрузка завершена");
		//loadAllFileIcon();
  	}
}



function createIcons(resultArray) {
    let td;
    let extList = "bmp, doc, docx, gif, jpg, mp3, pdf, png, tif, tiff, txt, xls, xlsx";
    for(let i = 0; i < resultArray.length; i++) {
        if(resultArray[i]['type'] == "1") td = document.getElementById(`${resultArray[i]['detid']}_scanvh_mailbox`);
        if(resultArray[i]['type'] == "2") td = document.getElementById(`${resultArray[i]['detid']}_scanish_mailbox`);
        let filetype = resultArray[i]["filename"].split(".").reverse()[0].toLowerCase();
        let a = document.createElement("a");
        let img = document.createElement("img");
        let textNode = document.createTextNode(" ");
        a.href = `/projectdata/mailbox/${resultArray[i]["prefix"]}_${resultArray[i]["filename"]}`;
        a.target = "_blank";
        extList.indexOf(filetype) < 0 ? img.src = `include/ico/unknow.png` : img.src = `include/ico/${filetype}.png`;
        img.title = `${resultArray[i]["filename"]}`;
        a.appendChild(img);
        td.appendChild(a);
        td.appendChild(textNode);
    }

    td = document.querySelectorAll('[id$="_scanvh_mailbox"], [id$="_scanish_mailbox"]');
    td.forEach(item => {
        let img = document.createElement("img");
        img.src = `include/new window.png`;
        img.style.float = "right";
        img.classList.add("button_field");
        if(zamok == 0) img.style.display = "none";
        item.appendChild(img);
    });
}

function mailfindbox(tabid) {
    if(document.getElementById("findlayer")) document.getElementById("findlayer").remove();
    let div = document.createElement("div");
    div.id = "findlayer";
    div.style.marginBottom = "10px";
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
            xhrLoad("xhrload", tabid, 0, "", findline.value);
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
            let today = `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate()}`;

            //if(parseInt(dateCtrl) <= parseInt(today) || (parseInt(dateCtrl) <= parseInt(today) && dateIsh == "")) item.parentNode.style.backgroundColor = "#eddddd"; //red
            //if(parseInt(dateCtrl) > parseInt(today) && dateIsh == "") item.parentNode.style.backgroundColor = "#ceedce"; //green
            if(parseInt(dateCtrl) <= parseInt(today) && dateIsh == "") item.parentNode.style.backgroundColor = "#eddddd"; //red
            if(parseInt(dateCtrl) > parseInt(today) && dateIsh == "") item.parentNode.style.backgroundColor = "#ceedce"; //green
            if(dateCtrl != "" && dateIsh != "") item.parentNode.style.backgroundColor = "";
        }
    })
}