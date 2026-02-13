import { iconLinkCreate } from "../commonTableFnc/iconLinkCreate.js";

export function tbodyCreate(data) {
    const tableHeader = /*html*/`
        <thead class="headerSection">
            <tr class="tableHeader">
                <td colspan="999">Контрагенты</td>
            </tr>

            <tr class="colHeader">
                <td class="headerlinenumber">№</td>
                <td class="nomeretap" data-column="nomeretap">Номер этапа нашего договора</td>
                <td class="namecontr" data-column="namecontr">Наименование контрагента</td>
                <td class="contentwork" data-column="contentwork">Содержание работ по этапам договора</td>
                <td class="nomdog" data-column="nomdog">Номер договора и дата</td>
                <td class="datestart" data-column="datestart">Дата начала</td>
                <td class="datestop" data-column="datestop">Дата окончания</td>
                <td class="otchet" data-column="otchet">Отчётные документы по этапам</td>
                <td class="otchetFile" data-column="otchetFile">Отчётные документы по этапам</td>
                <td class="prim" data-column="prim">Примечание</td>
            </tr>
        </thead>
    `;
    let tbody = "";
    if(data) data.forEach((item, i) => {
        let tr = /*html*/`
        <tr data-id="${item.id}">
            <td class="linenumber" data-id="${item.id}" data-table="contragent">${i+1}</td>
            <td class="nomeretap editable" data-column="nomeretap">${item.nomeretap}</td>
            <td class="namecontr editable" data-column="namecontr">${item.namecontr}</td>
            <td class="contentwork editable" data-column="contentwork">${item.contentwork}</td>
            <td class="nomdog editable" data-column="nomdog">${item.nomdog}</td>
            <td class="datestart" data-column="datestart">
                <input class="dateinput" type="text" readonly="readonly" value="${item.datestart}">
            </td>
            <td class="datestop" data-column="datestop">
                <input class="dateinput" type="text" readonly="readonly" value="${item.datestop}">
            </td>
            <td class="otchet editable" data-column="otchet">${item.otchet}</td>
            <td class="otchetFile fileLoader" data-column="otchetFile" data-type = "1">${iconLinkCreate(item.otchetFile)}</td>
            <td class="prim editable" data-column="prim">${item.prim}</td>
        </tr>`;

        tbody += tr;
    });
    
    return /*html*/`
        ${tableHeader}
        <tbody>${tbody}</tbody>
    `;
}