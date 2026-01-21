import { createTable } from "../commonTableFnc/createTable.js";
import { findInTable } from "../commonTableFnc/findInTable.js";
import { varControlEvt } from "../commonTableFnc/varControl.js";
import { state } from "../commonTableFnc/state.js";
import { editFunctions } from "../commonTableFnc/editFunctions.js";
import { createContextMenu } from "./createContextMenu.js";
import { tbodyCreate } from "./tbodyCreate.js";

export function docwork(izdelieid) {
    const tabInfo = {
        izdelieid: izdelieid,
        page: -1,
        tabName: 'docwork',
        dataTable: 'docwork',
        layer: '.tableBox',
        contextName: 'docwork-context',
        tbody: tbodyCreate,
        contextMenu: createContextMenu,
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