import { state } from "../commonTableFnc/state.js";
import { createStorage } from "../commonTableFnc/createStorage.js";

export function hideColumn(columnClassName, groupHeader, buttonName, sort) {
    const table = document.querySelector(`.${state.tabInfo.tabName}`)
    const colHeader = table.querySelector(`.colHeader .${columnClassName}`);
    const column = table.querySelectorAll(`.colHeader .${columnClassName}, tbody tr .${columnClassName}`);
    const header = table.querySelector(groupHeader);

    const appStorage = createStorage('controlMail');

    if(!appStorage.get(columnClassName)) hideCol(column, header, columnClassName, buttonName, sort);
    
    colHeader.addEventListener("click", () => {
        appStorage.remove(columnClassName);
        hideCol(column, header, columnClassName, buttonName, sort);
    })
}

function sortItems(node) {
    const items = Array.from(node.children);
    items.sort((a, b) => {
        return Number(a.dataset.sort) - Number(b.dataset.sort);
    })
    items.forEach(el => node.appendChild(el));
}

function hideCol(column, header, columnClassName, buttonName, sort) {
    const appStorage = createStorage('controlMail');
    column.forEach(item => {
        item.classList.add("hideColumn")
    })
    state.headerColSpan();

    if(!header.querySelector(".linkPanel")) {
        const linkPanel = /*html*/`
            <div class="linkPanel"></div>
        `;
        const fragment = state.createHTML(linkPanel);
        header.append(fragment);
    }

    const minilink = /*html*/`
        <div class="iconbutton ${columnClassName}" data-sort="${sort}">${buttonName}</div>
    `;
    const fragment = state.createHTML(minilink);
    const linkPanel = header.querySelector(".linkPanel");
    linkPanel.append(fragment);

    sortItems(linkPanel);

    header.querySelector(`.${columnClassName}`).addEventListener("click", (e) => {
        e.target.closest(`.${columnClassName}`).remove();
        appStorage.set(columnClassName, 1);
        column.forEach(item => {
            item.classList.remove("hideColumn")
        })
        state.headerColSpan();
    });
}