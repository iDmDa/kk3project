import { createTable } from "../commonTableFnc/createTable.js";
import { findInTable } from "../commonTableFnc/findInTable.js";
import { state } from "../commonTableFnc/state.js";
import { createContextMenu } from "./createContextMenu.js";
import { tbodyCreate } from "./tbodyCreate.js";

export function izv(izdelieid) {
    const tabInfo = {
        izdelieid: izdelieid,
        page: -1,
        tabName: 'izv',
        dataTable: 'docwork',
        layer: '.tableBox',
        contextName: 'izv-context',
        hide: 0,
        sortRule: 'byNumber',
        tbody: tbodyCreate,
        contextMenu: createContextMenu,
        callback: sortRules,
    }

    state.additionalFields = {
        doctype: 1,
    }

    const content = /*html*/`
        <div class="findBox"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.append(maintable);

    state.tabInfo = tabInfo;

    createTable(tabInfo);
    findInTable({layer: ".findBox"});
}

function sortRules() {
    const table = document.querySelector(".tableBox .izv");
    table.addEventListener("click", (e) => {
        if(e.target.classList.contains("numii") && e.target.closest(".colHeader")) {
            state.tabInfo.sortRule = 'byNumber';
            alert(state.tabInfo.sortRule);
        }
        if(e.target.classList.contains("date") && e.target.closest(".colHeader")) {
            state.tabInfo.sortRule = 'byDate';
            alert(state.tabInfo.sortRule)
        };
    })
}