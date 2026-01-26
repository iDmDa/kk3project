import { iconLinkCreate } from "../commonTableFnc/iconLinkCreate.js";

export function tbodyCreate(data) {
    const tableHeader = /*html*/`
        <thead class="headerSection">
            <tr class="tableHeader">
                <td colspan="999">Переписка</td>
            </tr>
            <tr class="groupHeader">
                <td class="inbox" colspan="1">Входящие</td>
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
                <td class="sumnormchasvh" data-column="sumnormchasvh">Трудоемкость</td>
                <td class="datereg" data-column="datereg">Рег. дата</td>
                <td class="nomerreg" data-column="nomerreg">Рег. номер</td>
                <td class="datecontrol" data-column="datecontrol">На контроле</td>
                <td class="prim" data-column="prim">Примечание</td>

                <td class="dateish" data-column="dateish">Дата</td>
                <td class="nomerish" data-column="nomerish">Номер</td>
                <td class="bottomTitle" data-column="adresish">Адресат</td>
                <td class="contentish" data-column="contentish">Краткое содержание</td>
                <td class="scanish" data-column="scanish">Скан</td>
                <td class="countlistish" data-column="countlistish">Кол. листов</td>
                <td class="sumnormchasish" data-column="sumnormchasish">Трудоемкость</td>
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
            <td class="sumnormchasvh editable" data-column="sumnormchasvh">${item.sumnormchasvh}</td>
            <td class="datereg" data-column="datereg">
                <input class="dateinput" type="text" readonly="readonly" value="${item.datereg}">
            </td>
            <td class="nomerreg editable" data-column="nomerreg">${item.nomerreg}</td>
            <td class="datecontrol" data-column="datecontrol">
                <input class="dateinput" type="text" readonly="readonly" value="${item.datecontrol}">
            </td>
            <td class="prim editable" data-column="prim">${item.prim}</td>


            <td class="dateish" data-column="dateish">
                <input class="dateinput" type="text" readonly="readonly" value="${item.dateish}">
            </td>
            <td class="nomerish editable" data-column="nomerish">${item.nomerish}</td>
            <td class="adresish editable" data-column="adresish">${item.adresish}</td>
            <td class="contentish editable" data-column="contentish">${item.contentish}</td>

            <td class="scanish" data-column="scanish" data-type = "2">${iconLinkCreate(item.scanish)}</td>

            <td class="countlistish editable" data-column="countlistish">${item.countlistish}</td>
            <td class="sumnormchasish editable" data-column="sumnormchasish">${item.sumnormchasish}</td>
            <td class="fioispish editable" data-column="fioispish">${item.fioispish}</td>
        </tr>`;

        tbody += tr;
    });
    
    return /*html*/`
        ${tableHeader}
        <tbody>${tbody}</tbody>
    `;
}