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
        tbody: tbodyCreate,
        contextMenu: createContextMenu,
    }

    state.additionalFields = {
        doctype: 1,
        hrenota: "test",
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

