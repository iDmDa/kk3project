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
                        if(value.length > 0) {
                            let result = "";
                            
                            for(let j = 0; j < value.length; j++) {
                                let filetype = value[j]["maskname"].split(".").reverse()[0];
                                let a = document.createElement("a");
                                let img = document.createElement("img");
                                a.href = `/include/ico/${filetype}.png`;
                                img.src = `include/ico/${filetype}.png`;
                                a.appendChild(img);
                                td.appendChild(a);
                                console.log(filetype); 
                                result += value[j]["maskname"];
                            }

                            
                            //td.innerHTML = result;
                        };
                        break;
                    
                    case "scanish1":
                        td.innerHTML = "ish";
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
	}

}