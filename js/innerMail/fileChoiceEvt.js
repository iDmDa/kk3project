import { createWindow } from "../common/createWindow.js";

export function fileChoiceEvt({evtPoint} = {}) {
    evtPoint.addEventListener("click", (e) => {
        if(e.target.classList.contains("addFileBtn")) {
            console.log("addFileBtn: ", e.target);
            const body = document.querySelector("body");
            createWindow({content: "<div>privet</div>"});
            
        }
    })
}
