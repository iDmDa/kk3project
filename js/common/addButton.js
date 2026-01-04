import { addNewLine } from "./addNewLine.js";

export function addButton({table, id, hide, callback} = {}) {
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
            callback: () => callback(),
        }
        console.log("adb: ", data);
        addNewLine(data);
    })
    return addButton;
}