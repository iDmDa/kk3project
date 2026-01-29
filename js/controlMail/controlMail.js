import { createTable } from "../commonTableFnc/createTable.js";
import { findInTable } from "../commonTableFnc/findInTable.js";
import { state } from "../commonTableFnc/state.js";
import { createContextMenu } from "./createContextMenu.js";
import { hideColumn } from "./hideColumn.js";
import { tbodyCreate } from "./tbodyCreate.js";
import { headerColSpan } from "./headerColSpan.js";
import { dateColor } from "./dateColor.js";
import { leftHeader } from "./leftHeader.js";

export function loadControlMail() {
    const tabInfo = {
        page: -1,
        tabName: 'controlMail',
        dataTable: 'mailbox',
        layer: '.tableBox',
        contextName: 'controlmail-context',
        hide: 0,
        tbody: tbodyCreate,
        hooks: {
            afterLoadTable: [
                () => createContextMenu(tabInfo.contextName),
                () => hideColumn("nomerreg", ".inbox", "Номер", 3),
                () => hideColumn("datereg", ".inbox", "Дата", 2),
                () => hideColumn("prim", ".inbox", "Прим", 4),
                () => hideColumn("sumnormchasvh", ".inbox", "Трудоемк.", 1),
                () => hideColumn("sumnormchasish", ".outbox", "Трудоемк.", 1),
                () => leftHeader(),
                () => state.headerColSpan(),
                () => dateColor(),
            ],
            txtEditorCallback: [
                () => dateColor(),
            ]
        }
    }
    state.additionalFields = {};
    state.headerColSpan = () => {return headerColSpan({rightLayer: ".dateish", colSpanHeader: ".inbox"});}
    
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
