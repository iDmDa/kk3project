class TableGenerator {

	tableID;
	layerID;

    bigTitle() {
        let tr = document.createElement("tr")
        let td = document.createElement("td");
        td.innerText = "Заголовок";
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
        td.colSpan = 3;
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
        td.innerText = "ppp";
        td.colSpan = 999;
        td.classList.add("table_header");

        tr.appendChild(td);
        return tr;
    }

    theadCreate() {
        let thead = document.createElement("thead");
        thead.appendChild(this.bigTitle());
        thead.appendChild(this.middleTitle());
        thead.appendChild(this.bottomTitle());
        return thead;
    }

    tbodyCreate() {
        let tr, td;
        let tbody = document.createElement("tbody");
        tbody.classList.add("table_body_block");

        for(let i = 0; i < 10; i++) {
            tr = document.createElement("tr");
            for(let j = 0; j < 10; j++) {
                td = document.createElement("td");
                td.innerText = ` (${i} ${j}) `;
                td.classList.add("simplefield");
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        }
        return tbody;
    }

	createTable() {
		let layer = document.getElementById(this.layerID);
		let table = document.createElement(this.tableID);
		table.id = this.tableID;
        table.classList.add("autotable");
		layer.append(table);
        table.appendChild(this.theadCreate());
        table.appendChild(this.tbodyCreate());
	}

}