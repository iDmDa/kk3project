import { createTable } from "../commonTableFnc/createTable.js";
import { findInTable } from "./findInTable.js";
import { varControlEvt } from "../commonTableFnc/varControl.js";
import { state } from "../commonTableFnc/state.js";
import { editFunctions } from "../commonTableFnc/editFunctions.js";
import { createContextMenu } from "./createContextMenu.js";
import { tbodyCreate } from "./tbodyCreate.js";

export function loadInnerMail(izdelieid) {
    const tabInfo = {
        izdelieid: izdelieid,
        page: -1,
        tabName: 'innerMail',
        dataTable: 'mailbox',
        layer: '.tableBox',
        contextName: 'innermail-context',
        tbody: tbodyCreate,
        contextMenu: createContextMenu,
    }
    const content = /*html*/`
        <div class="findBoxInnerMail"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.append(maintable);

    state.openStatus = window.openStatus;
    state.tabInfo = tabInfo;

    createTable(tabInfo);
    findInTable({layer: ".findBoxInnerMail"});

    varControlEvt({varName: "openStatus", callback: (val) => {
        editFunctions({openStatus: val, ...tabInfo});
        state.openStatus = val;
    }});


}

