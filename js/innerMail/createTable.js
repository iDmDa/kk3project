import { iconLinkCreate } from "../commonTableFnc/iconLinkCreate.js";
import { listNum } from "../commonTableFnc/listNum.js";
import { state } from "../commonTableFnc/state.js";
import { editFunctions } from "../commonTableFnc/editFunctions.js";
import { dataTransfer } from "../commonTableFnc/dataTransfer.js";
import { txtEditor } from "../commonTableFnc/txtEditor.js";
import { fileLoaderWindow } from "../commonTableFnc/fileLoaderWindow.js";
import { createContextMenu } from "./createContextMenu.js";
import { tbodyCreate } from "./tbodyCreate.js";

export function createTable(ctx = {}) {
    const {layer, tabName, dataTable, izdelieid, page, filter, scrollPos = -1, callback = () => {}} = ctx;
    state.mainTable = (patch = {}) => createTable({...ctx, ...patch});

    // varControlEvt({varName: "openStatus", callback: (val) => {
    //     console.log("Триггер varControlEvt: ", ctx)
    //     editFunctions({openStatus: val, reload: () => state.mainTable()});
    //     state.openStatus = val;
    // }});

    dataTransfer({...ctx, fl: tabName}).then(data => {
        console.log("crt: ", data);
        const table = /*html*/`
            <table id="table_${izdelieid}" class="${tabName}" data-table="${dataTable}" data-id="${izdelieid}">
                ${tbodyCreate(data[0])}
            </table>
        `;

        const fragment = document.createRange().createContextualFragment(table);
        const mainframe = document.querySelector(layer);

        let newPos;
        if(scrollPos === 1) newPos = mainframe.scrollTop;
        mainframe.innerHTML = "";
        mainframe.append(fragment);
        mainframe.append(listNum({allPages: data.pages, activePage: page, clkEvt: (data) => {
            //createTable({...ctx, page: data - 1}) //Функция будет вызвана по клику номера страницы
            state.mainTable({page: data - 1, scrollPos: -1});
        }})); //Поле со счетчиком страниц
        
        $(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });

        editFunctions({openStatus: window.openStatus, reload: () => {
            //createTable({...ctx, page: -1});
            state.mainTable({page: -1});
        } });

        createContextMenu();

        /* анимация удаления строки из таблицы
        document.querySelectorAll("tr").forEach(item => {
            item.addEventListener("click", () => {
                item.classList.add("hide");
                setTimeout(() => {
                    item.remove(); // или display: none
                }, 200);
            })
        })*/

        if(scrollPos === -1) mainframe.scrollTop = mainframe.scrollHeight - mainframe.clientHeight; //Прокрутка страницы вниз
        else mainframe.scrollTop = newPos;
        //mainframe.scrollTo({ top: mainframe.scrollHeight, behavior: 'smooth' }); //Прокрутка вниз плавно

        const maintable = document.querySelector(".innerMail");
        txtEditor(maintable);
        fileLoaderWindow({evtPoint: maintable})
        
        callback();

    });
}