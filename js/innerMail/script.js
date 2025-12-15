import { iconLinkCreate } from "./iconLinkCreate.js";
import { loadDBTable } from "./loadBDTable.js";

export function loadInnerMail(izdelieid, page = -1) {

    function tbodyCreate(data) {
        let tbody = "";
        console.log("data: ", data[0])
        if(data[0]) data[0].forEach((item, i) => {
            let tr = /*html*/`
            <tr>
                <td class="nomer">${i+1}</td>
                <td class="datevh" data-column="datevh">${item.datevh}</td>
                <td class="nomervh" data-column="nomervh">${item.nomervh}</td>
                <td class="adresvh" data-column="adresvh">${item.adresvh}</td>
                <td class="contentvh" data-column="contentvh">${item.contentvh}</td>
                <td class="scanvh" data-column="scanvh">${iconLinkCreate(item.scanvh)}</td>
                <td class="countlistvh" data-column="countlistvh">${item.countlistvh}</td>
                <td class="sumnormchasvh" data-column="sumnormchasvh">${item.sumnormchasvh}</td>

                <td class="dateish" data-column="dateish">${item.dateish}</td>
                <td class="nomerish" data-column="nomerish">${item.nomerish}</td>
                <td class="bottomTitle" data-column="adresish">${item.adresish}</td>
                <td class="contentish" data-column="contentish">${item.contentish}</td>
                <td class="scanish" data-column="scanish">${iconLinkCreate(item.scanish)}</td>
                <td class="countlistish" data-column="countlistish">${item.countlistish}</td>
                <td class="sumnormchasish" data-column="sumnormchasish">${item.sumnormchasish}</td>
                <td class="fioispish" data-column="fioispish">${item.fioispish}</td>
            </tr>`;

            tbody += tr;
        });
        
        return `<tbody>${tbody}</tbody>`;
    }

    const varframe = document.getElementById("varframe");
    const tableHeader = /*html*/`
        <thead class="table_header_block" id="table_header_block">
            <tr class="tableHeader">
                <td colspan="999">Внутренняя переписка и служебные записки</td>
            </tr>
            <tr class="groupHeader">
                <td class="inbox" colspan="8">Входящие</td>
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
                <td class="sumnormchasvh" data-column="sumnormchasvh">Трудо-<br>емкость</td>

                <td class="dateish" data-column="dateish">Дата</td>
                <td class="nomerish" data-column="nomerish">Номер</td>
                <td class="bottomTitle" data-column="adresish">Адресат</td>
                <td class="contentish" data-column="contentish">Краткое содержание</td>
                <td class="scanish" data-column="scanish">Скан</td>
                <td class="countlistish" data-column="countlistish">Кол. листов</td>
                <td class="sumnormchasish" data-column="sumnormchasish">Трудо-<br>емкость</td>
                <td class="fioispish" data-column="fioispish">ФИО исполнителя</td>
            </tr>
        </thead>
    `;

     
    loadDBTable(izdelieid, page).then(data => {

        console.log("Данные получены: ", data);

        const table = /*html*/`
            <table id="table_${izdelieid}" class="innerMail">
                ${tableHeader}
                ${tbodyCreate(data)}
            </table>
        `;

        const maintable = document.createRange().createContextualFragment(table);
        varframe.append(maintable);
    });


}