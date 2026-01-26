import { createTable } from "../commonTableFnc/createTable.js";
import { findInTable } from "../commonTableFnc/findInTable.js";
import { state } from "../commonTableFnc/state.js";
import { createContextMenu } from "./createContextMenu.js";
import { tbodyCreate } from "./tbodyCreate.js";

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
                () => hideColumn(".nomerreg"),
                () => hideColumn(".datereg"),
                () => hideColumn(".prim"),
                () => state.verticalSeparator(),
            ],
        }
    }
    state.additionalFields = {};
    state.verticalSeparator = () => {return verticalSeparator(".dateish", ".inbox");}
    
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

function hideColumn(columnClassName) {
    const table = document.querySelector(`.${state.tabInfo.tabName}`)
    const colHeader = table.querySelector(`.colHeader ${columnClassName}`);
    const column = table.querySelectorAll(`.colHeader ${columnClassName}, tbody tr ${columnClassName}`);
    
    colHeader.addEventListener("click", () => {
        column.forEach(item => {
            item.classList.add("hideColumn")
        })
        state.verticalSeparator();
    })
}

function verticalSeparator(rightColName, groupHeader) {
    const table = document.querySelector(`.${state.tabInfo.tabName}`);
    const column = table.querySelector(rightColName); //td:nth-child(2)
    const header = table.querySelector(groupHeader);

    const cs = document.querySelector(rightColName).closest("tr").querySelectorAll("td");
    let count = 0;
    for (const i in [...cs]) {
        if(i == column.cellIndex) break;
        if(!cs[i].classList.contains("hideColumn")) count++
    }
    header.colSpan = count;
}
