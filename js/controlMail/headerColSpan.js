import { state } from "../commonTableFnc/state.js";

export function headerColSpan(ctx = {}) {
    const {rightLayer, colSpanHeader} = ctx;
    const table = document.querySelector(`.${state.tabInfo.tabName}`);
    const column = table.querySelector(rightLayer); //td:nth-child(2)
    const header = table.querySelector(colSpanHeader);

    const cs = document.querySelector(rightLayer).closest("tr").querySelectorAll("td");
    let count = 0;
    for (const i in [...cs]) {
        if(i == column.cellIndex) break;
        if(!cs[i].classList.contains("hideColumn")) count++
    }
    header.colSpan = count;
}