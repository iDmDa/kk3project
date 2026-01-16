import { addButton } from "../common/addButton.js";
import { createTable } from "./createTable.js";
import { findInTable } from "./findInTable.js";
import { txtEditor } from "../common/txtEditor.js";
import { varControlEvt } from "../common/varControl.js";
import { state } from "../common/state.js";
import { editFunctions } from "./editFunctions.js";
import { fileLoaderWindow } from "./fileLoaderWindow.js";

export function loadInnerMail(izdelieid, page = -1) {

    const content = /*html*/`
        <div class="findBoxInnerMail"></div>
        <div class="tableBox"></div>
    `;

    const varframe = document.getElementById("varframe");
    const maintable = document.createRange().createContextualFragment(content);
    varframe.append(maintable);

    state.openStatus = window.openStatus;
    //console.log("(script)state.openStatus: ", state.openStatus);

    createTable({layer: ".tableBox", izdelieid: izdelieid, page: page})
    
    findInTable({layer: ".findBoxInnerMail"});

    //action(window.openStatus);
    varControlEvt({varName: "openStatus", callback: (val) => {
        console.log("Триггер varControlEvt")
        editFunctions({openStatus: val, reload: () => createTable({layer: ".tableBox", izdelieid: izdelieid, page: page})});
        state.openStatus = val;
    }});

    const tableBox = document.querySelector(".tableBox");
    txtEditor(tableBox);
    fileLoaderWindow({evtPoint: tableBox})
}

