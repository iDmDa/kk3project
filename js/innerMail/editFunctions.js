import { addButton } from "../common/addButton.js";

export function editFunctions(ctx = {}) {
    const {openStatus, reload} = ctx;
    const tableBox = document.querySelector(".tableBox");
    const table = document.querySelector(".innerMail");
    const editable = table.querySelectorAll(".editable");
    const dateinput = table.querySelectorAll(".dateinput");

    const tabInfo = {
        table: table.dataset.table,
        id: table.dataset.id,
        hide: 0,
        reload: () => reload(),
    }

    if(openStatus === "1") {
        editable.forEach(item => item.setAttribute("contenteditable", "true"))
        dateinput.forEach(item => item.removeAttribute("readonly"))
        table.after(addButton(tabInfo));
        scanIco({icon: "on"});
    }
    if(openStatus !== "1") {
        editable.forEach(item => item.removeAttribute("contenteditable", "true"))
        dateinput.forEach(item => item.setAttribute("readonly", "readonly"))
        tableBox.querySelector(".addButton")?.remove(); // '?' проверяет существование объекта и если есть запускает функцию
        scanIco({icon: "off"});
    }

    function scanIco({icon} = {}) {
        const scanCell = table.querySelectorAll("tbody .scanvh, tbody .scanish");
        const addFileBtn = /*html*/`
            <img class="addFileBtn" src="./include/new window.png" style="float: right;">
        `;
        if(icon === "on") {
            scanCell.forEach(item => {
                const fragment = document.createRange().createContextualFragment(addFileBtn);
                item.append(fragment);
            })
        }
        if(icon === "off") {
            const icons = document.querySelectorAll(".addFileBtn");
            icons.forEach(item => item.remove());
        }
    }
}
