import { state } from "../commonTableFnc/state.js";

export function verticalSeparator(rightColName, groupHeader) {
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