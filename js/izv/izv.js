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
        sortRule: 'byNumber',
        tbody: tbodyCreate,
        contextMenu: createContextMenu,
        callback: sortRules,
    }

    state.additionalFields = {
        doctype: 1,
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

function sortRules() {
    const eventPoint = document.querySelector(".tableBox .izv .colHeader");
    const NumiiCol = eventPoint.querySelector(".colHeader .numii");
    const DateCol = eventPoint.querySelector(".colHeader .date");
    const newNumii = /*html*/`
        <div class="extraColumn">
            <div>${NumiiCol.innerText}</div>
            <div class="arrow">${state.tabInfo.sortRule === 'byNumber' ? '<span>↓</span>' : '↕'}</div>
        </div>
    `;

    const newDate = /*html*/`
        <div class="extraColumn">
            <div>${DateCol.innerText}</div>
            <div class="arrow">${state.tabInfo.sortRule === 'byDate' ? '<span>↓</span>' : '↕'}</div>
        </div>
    `;

    const fragmentNumii = document.createRange().createContextualFragment(newNumii);
    const fragmentDate = document.createRange().createContextualFragment(newDate);

    NumiiCol.replaceChildren(fragmentNumii);
    DateCol.replaceChildren(fragmentDate);

    eventPoint.addEventListener("click", (e) => {
        if(e.target.closest(".numii")) {
            state.mainTable({scrollPos: 1, sortRule: 'byNumber'});
        }
        if(e.target.closest(".date")) {
            state.mainTable({scrollPos: 1, sortRule: 'byDate'});
        };
    })
}