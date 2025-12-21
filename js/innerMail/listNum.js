import { createTable } from "./createTable.js";

export function listNum({allPages, izdelieid, activePage, action} = {}) {
    if(activePage == -1) activePage = allPages - 1;
    let pages = "";
    for (let i = 0; i < allPages; i++) {
        const page = /*html*/`
            <div class="menuitem ${activePage == i ? "menuactive": ""}">${i+1}</div>
        `;
        pages += page;
    }

    const fragment = document.createRange().createContextualFragment(/*html*/`
        <div class="pagesBlock">${pages}</div>
    `);

    const pageBlock = fragment.querySelector(".pagesBlock");
    pageBlock.addEventListener("click", (e) => {
        console.log("listy", e.target.innerText);
        createTable({layer: ".tableBox", izdelieid: izdelieid, page: e.target.innerText - 1, action: () => action()});
    })

    return fragment;
}