import { addButton } from "../common/addButton.js";
import { createTable } from "./createTable.js";
import { findInTable } from "./findInTable.js";
import { txtEditor } from "../common/txtEditor.js";
import { varControlEvt } from "../common/varControl.js";

export function loadInnerMail(izdelieid, page = -1) {

    const content = /*html*/`
        <div class="findBoxInnerMail"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.append(maintable);
        
    createTable({layer: ".tableBox", izdelieid: izdelieid, page: page, action: () => action({value: window.openStatus})});
    findInTable({layer: ".findBoxInnerMail"});

    //action(window.openStatus);
    varControlEvt({varName: "openStatus", callback: (val) => action({value: val})});

    const table = document.querySelector(".tableBox");
    txtEditor(table);
}

function action({value} = {}) {
    const tableBox = document.querySelector(".tableBox");
    const table = document.querySelector(".innerMail");
    const editable = table.querySelectorAll(".editable");

    const tabInfo = {
        table: table.dataset.table,
        id: table.dataset.id,
        hide: 0,
        callback: () => createTable({layer: ".tableBox", izdelieid: table.dataset.id, page: -1, action: () => action({value: value})}),
    }

    if(value === "1") {
        console.log("editable ", editable)
        editable.forEach(item => {
            item.setAttribute("contenteditable", "true");
        })
        table.after(addButton(tabInfo));
    }
    if(value !== "1") {
        editable.forEach(item => {
            item.removeAttribute("contenteditable", "true");
        })
        tableBox.querySelector(".addButton")?.remove(); // '?' проверяет существование объекта и если есть запускает функцию
    }
}
