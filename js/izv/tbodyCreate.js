import { iconLinkCreate } from "../commonTableFnc/iconLinkCreate.js";
import { state } from "../commonTableFnc/state.js";

export function tbodyCreate(data) {
    console.log("data tb: ", data)
    const tableHeader = /*html*/`
        <thead class="headerSection">
            <tr class="tableHeader">
                <td colspan="999">Извещения</td>
            </tr>
            <tr class="groupHeader">
                <td class="moddleHeader" colspan="999">${izdName(state.tabInfo['izdelieid'])}</td>
            </tr>
            <tr class="colHeader">
                <td class="headerlinenumber">№</td>
                <td class="numii" data-column="numii">№ ИИ</td>
                <td class="editdoc" data-column="editdoc">Корректируемые документы</td>
                <td class="naimenovenie" data-column="naimenovenie">Краткое содержание корректировки</td>
                <td class="reason" data-column="reason">Основание для корректировки</td>
                <td class="fio" data-column="fio">Исполнитель</td>
                <td class="otd" data-column="otd">Подразделение</td>
                <td class="codii" data-column="codii">Код</td>
                <td class="zadel" data-column="zadel">Указание о заделе</td>
                <td class="vnedrenie" data-column="vnedrenie">Указание о внедрении</td>
                <td class="date" data-column="date">Дата выпуска</td>
                <td class="numish" data-column="numish">№ исх. на отправку дубликатов</td>
                <td class="scan" data-column="scan">Скан</td>
                <td class="trudoemc" data-column="trudoemc">Трудо-<br>емкость</td>
                <td class="prim" data-column="prim">Примечание</td>
            </tr>
        </thead>
    `;
    let tbody = "";
    if(data) data.forEach((item, i) => {
        let tr = /*html*/`
        <tr data-id="${item.id}">
            <td class="linenumber" data-id="${item.id}" data-table="docwork">${i+1}</td>
            <td class="numii editable" data-column="numii">${item.numii}</td>
            <td class="editdoc editable" data-column="editdoc">${item.editdoc}</td>
            <td class="naimenovenie editable" data-column="naimenovenie">${item.naimenovenie}</td>
            <td class="reason editable" data-column="reason">${item.reason}</td>
            <td class="fio editable" data-column="fio">${item.fio}</td>
            <td class="otd editable" data-column="otd">${item.otd}</td>
            <td class="codii editable" data-column="codii">${item.codii}</td>
            <td class="zadel editable" data-column="zadel">${item.zadel}</td>
            <td class="vnedrenie editable" data-column="vnedrenie">${item.vnedrenie}</td>
            <td class="date" data-column="date">
                <input class="dateinput" type="text" readonly="readonly" value="${item.date}">
            </td>
            <td class="numish editable" data-column="numish">${item.numish}</td>

            <td class="scan fileLoader" data-column="scan" data-type = "2">${iconLinkCreate(item.scan)}</td>
            <td class="trudoemc fileLoader" data-column="trudoemc" data-type = "1">${iconLinkCreate(item.trudoemc)}</td>

            <td class="prim editable" data-column="prim">${item.prim}</td>
        </tr>`;

        tbody += tr;
    });
    
    return /*html*/`
        ${tableHeader}
        <tbody>${tbody}</tbody>
    `;
}

function izdName(data) {
    return document.querySelector(`#tree_layer [data-izdnomer='${data}']`).innerText;
}