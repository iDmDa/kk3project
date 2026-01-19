import { dataTransfer } from "./dataTransfer.js";
import { state } from "./state.js";

export function addButton({table, id, hide, reload} = {}) {
    const button = /*html*/`
        <div class="addButton">
            <img src="./include/addline.png">
        </div>
    `;

    const addButton = document.createRange().createContextualFragment(button);

    addButton.querySelector(".addButton img").addEventListener("click", () => {
        const data = {
            table: table, 
            id: id, 
            hide: hide,
            fl: "addNewLine",
        }
        dataTransfer(data).then(() => {
            state.mainTable({page: -1})
            //reload();
        });
    })
    return addButton;
}