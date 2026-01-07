import { createWindow } from "../common/createWindow.js";
import { createFileList } from "./createFileList.js";

export function fileChoiceEvt({evtPoint} = {}) {
    evtPoint.addEventListener("click", (e) => {
        if(e.target.classList.contains("addFileBtn")) {
            console.log("addFileBtn: ", e.target.closest("tr").dataset.id, e.target.closest("td").dataset.column);
            const detid = e.target.closest("tr").dataset.id;
            const type = e.target.closest("td").dataset.type;
            const tableName = e.target.closest("table").dataset.table;
            createWindow({contentLoader: () => createFileList({layer: ".tmp_content", detid: detid, type: type, tableName: tableName})});
            
        }
    })
}