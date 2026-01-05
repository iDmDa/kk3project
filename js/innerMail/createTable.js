import { iconLinkCreate } from "./iconLinkCreate.js";
import { listNum } from "./listNum.js?v=1";
import { loadData } from "../common/loadData.js";
import { varControlEvt } from "../common/varControl.js";
import { state } from "./state.js";
import { editFunctions } from "./editFunctions.js";

export function createTable(ctx = {}) {
    const {layer, izdelieid, page} = ctx;
    function tbodyCreate(data) {
        let tbody = "";
        //console.log("data: ", data)
        if(data) data.forEach((item, i) => {
            let tr = /*html*/`
            <tr data-id="${item.id}">
                <td class="nomer" data-id="${item.id}">${i+1}</td>
                <td class="datevh" data-column="datevh">
                    <input class="dateinput" type="text" readonly="readonly" value="${item.datevh}">
                </td>
                <td class="nomervh editable" data-column="nomervh">${item.nomervh}</td>
                <td class="adresvh editable" data-column="adresvh">${item.adresvh}</td>
                <td class="contentvh editable" data-column="contentvh">${item.contentvh}</td>
                <td class="scanvh" data-column="scanvh">${iconLinkCreate(item.scanvh)}</td>
                <td class="countlistvh editable" data-column="countlistvh">${item.countlistvh}</td>

                <td class="dateish" data-column="dateish">
                    <input class="dateinput" type="text" readonly="readonly" value="${item.dateish}">
                </td>
                <td class="nomerish editable" data-column="nomerish">${item.nomerish}</td>
                <td class="adresish editable" data-column="adresish">${item.adresish}</td>
                <td class="contentish editable" data-column="contentish">${item.contentish}</td>
                <td class="scanish" data-column="scanish">${iconLinkCreate(item.scanish)}</td>
                <td class="countlistish editable" data-column="countlistish">${item.countlistish}</td>
                <td class="fioispish editable" data-column="fioispish">${item.fioispish}</td>
            </tr>`;

            tbody += tr;
        });
        
        return `<tbody>${tbody}</tbody>`;
    }

    const mainframe = document.querySelector(layer);
    mainframe.innerHTML = "";
    const tableHeader = /*html*/`
        <thead class="table_header_block" id="table_header_block">
            <tr class="tableHeader">
                <td colspan="999">Внутренняя переписка и служебные записки</td>
            </tr>
            <tr class="groupHeader">
                <td class="inbox" colspan="7">Входящие</td>
                <td class="outbox" colspan="999">Исходящие</td>
            </tr>
            <tr class="colHeader">
                <td class="nomer">№</td>
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
     
    loadData(izdelieid, page).then(data => {

        console.log("Данные получены: ", data);

        const table = /*html*/`
            <table id="table_${izdelieid}" class="innerMail" data-table="mailbox" data-id="${izdelieid}">
                ${tableHeader}
                ${tbodyCreate(data[0])}
            </table>
        `;

        const maintable = document.createRange().createContextualFragment(table);

        mainframe.append(maintable);
        mainframe.append(listNum({allPages: data.pages, activePage: page, clkEvt: (data) => {
            createTable({...ctx, page: data - 1}) //Функция будет вызвана по клику номера страницы
        }})); //Поле со счетчиком страниц
        
        $(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });

        editFunctions({openStatus: window.openStatus, reload: () => createTable({...ctx, page: -1})});
        
        console.log("(createTable)window.openStatus: ", window.openStatus)
        console.log("(createTable)state.openStatus: ", state.openStatus)
        console.log("(createTable)ctx: ", ctx)

        mainframe.scrollTop = mainframe.scrollHeight - mainframe.clientHeight; //Прокрутка страницы вниз
        //mainframe.scrollTo({ top: mainframe.scrollHeight, behavior: 'smooth' }); //Прокрутка вниз плавно

    });
}