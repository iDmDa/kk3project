import { listNum } from "./listNum.js";
import { state } from "./state.js";
import { editFunctions } from "./editFunctions.js";
import { dataTransfer } from "./dataTransfer.js";
import { txtEditor } from "./txtEditor.js";
import { fileLoaderWindow } from "./fileLoaderWindow.js";

export function createTable(ctx = {}) {
    const {
        layer, tabName, contextName, dataTable, izdelieid, page, filter, 
        scrollPos = -1, 
        tbody = () => {}, 
        contextMenu = () => {}, 
        callback = () => {},
    } = ctx;

    state.mainTable = (patch = {}) => createTable({...ctx, ...patch});

    dataTransfer({...ctx, fl: tabName}).then(data => {
        console.log("crt: ", data, ctx);
        const table = /*html*/`
            <table id="table_${izdelieid}" class="moduleTable ${tabName}" data-table="${dataTable}" data-id="${izdelieid}">
                ${tbody(data[0])}
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

        editFunctions({openStatus: window.openStatus, ...ctx });

        contextMenu(contextName);

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

        const maintable = document.querySelector(`.${tabName}`);
        txtEditor(maintable);
        fileLoaderWindow(maintable)
        
        callback();

    });
}