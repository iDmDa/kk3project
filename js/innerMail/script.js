import { createTable } from "./createTable.js?v=4";
import { findInTable } from "./findInTable.js";
import { varControlEvt } from "./varControl.js?v=1";

export function loadInnerMail(izdelieid, page = -1) {

    const content = /*html*/`
        <div class="findBoxInnerMail"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.append(maintable);
        
    createTable({layer: ".tableBox", izdelieid: izdelieid, page: page, action: () => action(window.openStatus)});
    findInTable({layer: ".findBoxInnerMail"});

    //action(window.openStatus);
    varControlEvt({varName: "openStatus", action: action});
}

function action(value) {
    const table = document.querySelector(".innerMail");
    const editable = table.querySelectorAll(".editable");
    if(value === "1") {
        console.log("editable ", editable)
        editable.forEach(item => {
            item.setAttribute("contenteditable", "true");
        })
    }
    if(value !== "1") {
        editable.forEach(item => {
            item.removeAttribute("contenteditable", "true");
        })
    }
}