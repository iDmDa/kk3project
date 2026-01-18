import { iconLinkCreate } from "../commonTableFnc/iconLinkCreate.js";
import { listNum } from "../commonTableFnc/listNum.js";
import { state } from "../commonTableFnc/state.js";
import { editFunctions } from "../commonTableFnc/editFunctions.js";
import { dataTransfer } from "../commonTableFnc/dataTransfer.js";
import { txtEditor } from "../commonTableFnc/txtEditor.js";
import { fileLoaderWindow } from "./fileLoaderWindow.js";
import { varControlEvt } from "../commonTableFnc/varControl.js";

export function createTable(ctx = {}) {
    const {layer, izdelieid, page, filter, scrollPos = -1, callback = () => {}} = ctx;
    state.mainTable = (patch = {}) => createTable({...ctx, ...patch});

    // varControlEvt({varName: "openStatus", callback: (val) => {
    //     console.log("Триггер varControlEvt: ", ctx)
    //     editFunctions({openStatus: val, reload: () => state.mainTable()});
    //     state.openStatus = val;
    // }});

    function tbodyCreate(data) {
        const tableHeader = /*html*/`
            <thead class="headerSection">
                <tr class="tableHeader">
                    <td colspan="999">Внутренняя переписка и служебные записки</td>
                </tr>
                <tr class="groupHeader">
                    <td class="inbox" colspan="7">Входящие</td>
                    <td class="outbox" colspan="999">Исходящие</td>
                </tr>
                <tr class="colHeader">
                    <td class="headerlinenumber">№</td>
                    <td class="datevh" data-column="datevh">Дата</td>
                    <td class="nomervh" data-column="nomervh">Номер</td>
                    <td class="adresvh" data-column="adresvh">Адресат</td>
                    <td class="contentvh" data-column="contentvh">Краткое содержание</td>
                    <td class="scanvh" data-column="scanvh">Скан</td>
                    <td class="countlistvh" data-column="countlistvh">Кол. листов</td>

                    <td class="dateish" data-column="dateish">Дата</td>
                    <td class="nomerish" data-column="nomerish">Номер</td>
                    <td class="bottomTitle" data-column="adresish">Адресат</td>
                    <td class="contentish" data-column="contentish">Краткое содержание</td>
                    <td class="scanish" data-column="scanish">Скан</td>
                    <td class="countlistish" data-column="countlistish">Кол. листов</td>
                    <td class="fioispish" data-column="fioispish">ФИО исполнителя</td>
                </tr>
            </thead>
        `;
        let tbody = "";
        if(data) data.forEach((item, i) => {
            let tr = /*html*/`
            <tr data-id="${item.id}">
                <td class="linenumber" data-id="${item.id}" data-table="mailbox">${i+1}</td>
                <td class="datevh" data-column="datevh">
                    <input class="dateinput" type="text" readonly="readonly" value="${item.datevh}">
                </td>
                <td class="nomervh editable" data-column="nomervh">${item.nomervh}</td>
                <td class="adresvh editable" data-column="adresvh">${item.adresvh}</td>
                <td class="contentvh editable" data-column="contentvh">${item.contentvh}</td>
                <td class="scanvh" data-column="scanvh" data-type = "1">${iconLinkCreate(item.scanvh)}</td>
                <td class="countlistvh editable" data-column="countlistvh">${item.countlistvh}</td>

                <td class="dateish" data-column="dateish">
                    <input class="dateinput" type="text" readonly="readonly" value="${item.dateish}">
                </td>
                <td class="nomerish editable" data-column="nomerish">${item.nomerish}</td>
                <td class="adresish editable" data-column="adresish">${item.adresish}</td>
                <td class="contentish editable" data-column="contentish">${item.contentish}</td>
                <td class="scanish" data-column="scanish" data-type = "2">${iconLinkCreate(item.scanish)}</td>
                <td class="countlistish editable" data-column="countlistish">${item.countlistish}</td>
                <td class="fioispish editable" data-column="fioispish">${item.fioispish}</td>
            </tr>`;

            tbody += tr;
        });
        
        return `/*html*/
            ${tableHeader}
            <tbody>${tbody}</tbody>
        `;
    }
     
    dataTransfer({...ctx, fl: "innerMail"}).then(data => {
        console.log("crt: ", data);
        const table = /*html*/`
            <table id="table_${izdelieid}" class="innerMail" data-table="mailbox" data-id="${izdelieid}">
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

function createContextMenu() {
    $.contextMenu('destroy', '.innermail-context');
    $.contextMenu({  //меню удаления
        selector: '.innermail-context',
        items: {
            delete: {
                name: 'Удалить',
                callback: function() {
                    if (prompt("Для подтверждения удаления введите '1'", 'Введите: 1') == true) {
                        const data = {
                            table: this[0].dataset.table,
                            column: 'hide',
                            id: this[0].dataset.id,
                            content: 1,
                            fl: 'txtSave',
                        }
                        dataTransfer(data).then(dt => {
                            state.mainTable();
                        });
                    }
                    else alert('Отмена удаления');
                }
            },
            moveToMail: {
                name: 'Перенести в раздел Переписка',
                callback: function() {
                    const data = {
                        table: this[0].dataset.table,
                        column: 'hide',
                        id: this[0].dataset.id,
                        content: 0,
                        fl: "txtSave",
                    }
                    dataTransfer(data).then(dt => {
                        state.mainTable();
                    });
                }
            },
            sep1: '---------',
            quit: {
                name: 'Выйти',
                callback: () => {}
            }
        }
    });
}