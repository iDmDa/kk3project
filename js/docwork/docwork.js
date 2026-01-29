import { createTable } from "../commonTableFnc/createTable.js";
import { findInTable } from "../commonTableFnc/findInTable.js";
import { state } from "../commonTableFnc/state.js";
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
        hooks: {
            afterLoadTable: [
                () => createContextMenu(tabInfo.contextName),
            ],
        }
    }
    state.additionalFields = {};
    
    const content = /*html*/`
        <div class="findBox"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = state.createHTML(content);
    varframe.replaceChildren(maintable);

    state.tabInfo = tabInfo;

    createTable(tabInfo);
    findInTable({layer: ".findBox"});
}