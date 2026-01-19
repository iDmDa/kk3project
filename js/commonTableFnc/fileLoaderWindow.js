import { createWindow } from "./createWindow.js";
import { createFileTable } from "./createFileTable.js";

export function fileLoaderWindow({evtPoint} = {}) {
    evtPoint.addEventListener("click", (e) => {
        if(e.target.classList.contains("addFileBtn")) {
            const data = {
                layer: ".tmp_content",
                detid: e.target.closest("tr").dataset.id,
                type: e.target.closest("td").dataset.type,
                tableName: e.target.closest("table").dataset.table,
            }
            createWindow({contentLoader: () => createFileTable(data)});
        }
    })
}