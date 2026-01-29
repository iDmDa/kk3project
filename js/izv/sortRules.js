import { state } from "../commonTableFnc/state.js";

export function sortRules() {
    const eventPoint = document.querySelector(".tableBox .izv .colHeader");
    const NumiiCol = eventPoint.querySelector(".colHeader .numii");
    const DateCol = eventPoint.querySelector(".colHeader .date");
    const newNumii = /*html*/`
        <div class="extraColumn">
            <div class="hName">${NumiiCol.innerText}</div>
            <div class="arrow">${state.tabInfo.sortRule === 'byNumber' ? '<span>↓</span>' : '↕'}</div>
        </div>
    `;

    const newDate = /*html*/`
        <div class="extraColumn">
            <div class="hName">${DateCol.innerText}</div>
            <div class="arrow">${state.tabInfo.sortRule === 'byDate' ? '<span>↓</span>' : '↕'}</div>
        </div>
    `;

    const fragmentNumii = state.createHTML(newNumii);
    const fragmentDate = state.createHTML(newDate);

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