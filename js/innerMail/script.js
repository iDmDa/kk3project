import { createTable } from "./createTable.js";
import { findInTable } from "./findInTable.js";
import { varControlEvt } from "../commonTableFnc/varControl.js";
import { state } from "../commonTableFnc/state.js";
import { editFunctions } from "../commonTableFnc/editFunctions.js";
import { txtEditor } from "../commonTableFnc/txtEditor.js";
import { fileLoaderWindow } from "./fileLoaderWindow.js";

export function loadInnerMail(ctx = {}) {
    const {izdelieid, page, tabName, dataTable} = ctx;
    const content = /*html*/`
        <div class="findBoxInnerMail"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.append(maintable);



    state.openStatus = window.openStatus;

    createTable({...ctx, layer: ".tableBox", tabName: "innerMail"});
    findInTable({layer: ".findBoxInnerMail"});

    varControlEvt({varName: "openStatus", callback: (val) => {
        console.log("Триггер varControlEvt: ", ctx)
        editFunctions({openStatus: val, reload: () => createTable({...ctx, layer: ".tableBox"})});
        state.openStatus = val;
    }});


}

