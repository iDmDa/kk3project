import { dataTransfer } from "./dataTransfer.js";
import { state } from "./state.js";

export function addButton(ctx = {}) {
    const {...tabInfo} = ctx;
    const button = /*html*/`
        <div class="addButton">
            <img src="./include/addline.png">
        </div>
    `;

    const addButton = state.createHTML(button);

    addButton.querySelector(".addButton img").addEventListener("click", () => {
        const data = {
            ...ctx,
            fl: "addNewLine",
        }
        dataTransfer(data).then(() => {
            state.mainTable({page: -1})
        });
    })
    return addButton;
}