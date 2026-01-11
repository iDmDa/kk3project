import { addButton } from "../common/addButton.js";

export function editFunctions(ctx = {}) {
    const {openStatus, reload} = ctx;
    const tableBox = document.querySelector(".tableBox");
    const table = document.querySelector(".innerMail");
    const editable = table.querySelectorAll(".editable");
    const dateinput = table.querySelectorAll(".dateinput");
    const linenumber = table.querySelectorAll(".linenumber")

    const tabInfo = {
        table: table.dataset.table,
        id: table.dataset.id,
        hide: 2,
        reload: () => reload(),
    }

    tableBox.querySelector(".addButton")?.remove();

    if(openStatus === "1") {
        editable.forEach(item => item.setAttribute("contenteditable", "true"))
        linenumber.forEach(item => item.classList.add("innermail-context"))
        dateinput.forEach(item => item.removeAttribute("readonly"))
        table.after(addButton(tabInfo));
        newWindowIcon({icon: "on"});
    }
    if(openStatus !== "1") {
        editable.forEach(item => item.removeAttribute("contenteditable", "true"))
        linenumber.forEach(item => item.classList.remove("innermail-context"))
        dateinput.forEach(item => item.setAttribute("readonly", "readonly"))
        tableBox.querySelector(".addButton")?.remove(); // '?' проверяет существование объекта и если есть запускает функцию
        newWindowIcon({icon: "off"});
    }

    function newWindowIcon({icon} = {}) {
        const scanCell = table.querySelectorAll("tbody .scanvh, tbody .scanish");
        const addFileBtn = /*html*/`
            <img class="addFileBtn" src="./include/new window.png" style="float: right;">
        `;

        document.querySelectorAll(".addFileBtn")?.forEach(item => item.remove());

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
