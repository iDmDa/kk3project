import { createTable } from "./createTable.js";
import { findInTable } from "./findInTable.js";
import { varControlEvt } from "../commonTableFnc/varControl.js";
import { state } from "../commonTableFnc/state.js";
import { editFunctions } from "../commonTableFnc/editFunctions.js";

export function loadInnerMail(izdelieid) {
    const tabInfo = {
        izdelieid: izdelieid,
        page: -1,
        tabName: 'innerMail',
        dataTable: 'mailbox',
        layer: '.tableBox',
    }
    const content = /*html*/`
        <div class="findBoxInnerMail"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.append(maintable);

    state.openStatus = window.openStatus;

    createTable(tabInfo);
    findInTable({layer: ".findBoxInnerMail"});

    varControlEvt({varName: "openStatus", callback: (val) => {
        editFunctions({openStatus: val, reload: () => createTable(tabInfo)});
        state.openStatus = val;
    }});


}

