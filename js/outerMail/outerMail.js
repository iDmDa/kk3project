import { createTable } from "../commonTableFnc/createTable.js";
import { findInTable } from "../commonTableFnc/findInTable.js";
import { state } from "../commonTableFnc/state.js";
import { createContextMenu } from "./createContextMenu.js";
import { hideColumn } from "./hideColumn.js";
import { tbodyCreate } from "./tbodyCreate.js";
import { headerColSpan } from "./headerColSpan.js";

export function loadOuterMail(izdelieid) {
    const tabInfo = {
        izdelieid: izdelieid,
        page: -1,
        tabName: 'outerMail',
        dataTable: 'mailbox',
        layer: '.tableBox',
        contextName: 'outermail-context',
        hide: 0,
        tbody: tbodyCreate,
        hooks: {
            afterLoadTable: [
                () => createContextMenu(tabInfo.contextName),
                () => hideColumn("nomerreg", ".inbox", "Номер", 2),
                () => hideColumn("datereg", ".inbox", "Дата", 1),
                () => hideColumn("prim", ".inbox", "Прим", 3),
                () => state.headerColSpan(),
            ],
        }
    }
    state.additionalFields = {};
    state.headerColSpan = () => {return headerColSpan(".dateish", ".inbox");}
    
    const content = /*html*/`
        <div class="findBox"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.replaceChildren(maintable);

    state.tabInfo = tabInfo;

    createTable(tabInfo);
    findInTable({layer: ".findBox"});
}
