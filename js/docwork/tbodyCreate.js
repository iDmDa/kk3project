import { iconLinkCreate } from "../commonTableFnc/iconLinkCreate.js";

export function tbodyCreate(data) {
    const tableHeader = /*html*/`
        <thead class="headerSection">
            <tr class="tableHeader">
                <td colspan="999">Документы и работы</td>
            </tr>
            <tr class="groupHeader">
                <td class="docs" colspan="4">Документы и работы</td>
                <td class="isp" colspan="2">Исполнители</td>
                <td class="trud" colspan="6">Трудоемкость</td>
                <!-- <td class="scan" colspan="2">Ссылки на документ или результат работы</td> -->
                <td class="scan" colspan="2">Ссылки</td>
                <td class="result" colspan="2">Готовность</td>
            </tr>
            <tr class="colHeader">
                <td class="headerlinenumber">№</td>
                <td class="naimenovenie" data-column="naimenovenie">Наименование</td>
                <td class="date" data-column="date">Дата</td>
                <td class="numstage" data-column="numstage">Номер этапа</td>
                <td class="otd" data-column="otd">Отдел</td>
                <td class="fio" data-column="fio">ФИО</td>
                <td class="codnorm" data-column="codnorm">Код <br>норма-<br>тива</td>
                <td class="normativtruda" data-column="normativtruda">Норма-<br>тив трудо-<br>емко-<br>сти</td>

                <td class="kolvoformatov" data-column="kolvoformatov">Кол-во форма-<br>тов</td>
                <td class="planchas" data-column="planchas">плано-<br>вая, час</td>
                <td class="ispolnchas" data-column="ispolnchas">испо-<br>лни-<br>теля, час</td>
                <td class="factchas" data-column="factchas">фактич. отче-<br>тная, час</td>
                <td class="chernovik" data-column="chernovik">Черно-<br>вик</td>
                <td class="scan" data-column="scan">Скани-<br>рован-<br>ный</td>
                <td class="gotovnost" data-column="gotovnost">% <br>готов-<br>ности</td>
                <td class="prim" data-column="prim">Примечание</td>
            </tr>
        </thead>
    `;
    let tbody = "";
    if(data) data.forEach((item, i) => {
        let tr = /*html*/`
        <tr data-id="${item.id}">
            <td class="linenumber" data-id="${item.id}" data-table="docwork">${i+1}</td>
            <td class="naimenovenie editable" data-column="naimenovenie">${item.naimenovenie}</td>
            <td class="date" data-column="date">
                <input class="dateinput" type="text" readonly="readonly" value="${item.date}">
            </td>
            <td class="numstage editable" data-column="numstage">${item.numstage}</td>

            <td class="otd editable" data-column="otd">${item.otd}</td>
            <td class="fio editable" data-column="fio">${item.fio}</td>

            <td class="codnorm editable" data-column="codnorm">${item.codnorm}</td>
            <td class="normativtruda editable" data-column="normativtruda">${item.normativtruda}</td>
            <td class="kolvoformatov editable" data-column="kolvoformatov">${item.kolvoformatov}</td>
            <td class="planchas editable" data-column="planchas">${item.planchas}</td>
            <td class="ispolnchas editable" data-column="ispolnchas">${item.ispolnchas}</td>
            <td class="factchas editable" data-column="factchas">${item.factchas}</td>

            <td class="chernovik fileLoader" data-column="chernovik" data-type = "1">${iconLinkCreate(item.chernovik)}</td>
            <td class="scan fileLoader" data-column="scan" data-type = "2">${iconLinkCreate(item.scan)}</td>

            <td class="gotovnost editable" data-column="gotovnost">${item.gotovnost}</td>
            <td class="prim editable" data-column="prim">${item.prim}</td>
        </tr>`;

        tbody += tr;
    });
    
    return /*html*/`
        ${tableHeader}
        <tbody>${tbody}</tbody>
    `;
}