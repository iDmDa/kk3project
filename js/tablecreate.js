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
        td.colSpan = 7;
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
        let td = document.createElement("td");
        for(let item in this.dbData[0]) {
            if(this.fieldList.indexOf(item) < 0) continue;
            td = document.createElement("td");
            td.innerHTML = `${this.dbData[0][item]}`;
            td.classList.add("table_header");
            td.dataset.table = item;
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
        let tr, td;
        let tbody = document.createElement("tbody");
        tbody.classList.add("table_body_block");

        for(let i = 1; i < this.dbData.length; i++) {
            tr = document.createElement("tr");
            for(let item in this.dbData[i]) {
                if(this.fieldList.indexOf(item) < 0) continue;
                td = document.createElement("td");
                td.innerHTML = `${this.dbData[i][item]}`;
                td.classList.add("simplefield");
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        }

        return tbody;
    }

    createNumberLine() {
        //let title = document.querySelectorAll(`#${tableID} .table_header`)[0].before(document.createElement("td"));
        let title = document.querySelectorAll(`#${this.tableID} .table_header`);
        let td = document.createElement("td");
        title[0].before(td);
        td.innerText = "№";
        td.classList.add("table_header");
        td.style.width = "25px";

        //document.querySelectorAll("#table_105 tbody tr")[1].children[0].before(document.createElement("td"))
        let len = document.querySelectorAll(`#${this.tableID} tbody tr`).length;
        for(let i = 0; i < len; i++) {
            td = document.createElement("td");
            td.innerText = i + 1;
            td.classList.add("table_nomeric_col");
            document.querySelectorAll(`#${this.tableID} tbody tr`)[i].children[0].before(td);
        }
    }

	createTable() {
		let layer = document.getElementById(this.layerID);
		let table = document.createElement("table");
		table.id = this.tableID;
        table.classList.add("autotable");
		layer.append(table);
        table.appendChild(this.theadCreate());
        table.appendChild(this.tbodyCreate());
        this.createNumberLine();
	}

}