import { createWindow } from "./createWindow.js";
import { createFileTable } from "./createFileTable.js";
import { state } from "./state.js";

export function fileLoaderWindow(evtPoint) {
    state.closeFileWindow = () => document.querySelectorAll(".lineSelected").forEach(item => item.classList.remove("lineSelected"));
    evtPoint.addEventListener("click", (e) => {
        if(e.target.classList.contains("addFileBtn")) {
            const data = {
                layer: ".tmp_content",
                detid: e.target.closest("tr").dataset.id,
                type: e.target.closest("td").dataset.type,
                tableName: e.target.closest("table").dataset.table,
            }
            
            state.closeFileWindow();
            e.target.closest("td").classList.add("lineSelected");
            
            createWindow({contentLoader: () => createFileTable(data)});
        }
    })
}