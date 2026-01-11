import { editFunctions } from "./editFunctions.js";
import { loadFileList } from "../common/loadFileList.js";
import { state } from "../common/state.js";
import { iconLinkCreate } from "./iconLinkCreate.js";
import { txtEditor } from "../common/txtEditor.js";
import { saveData } from "../common/saveData.js";

export function createFileTable(ctx = {}) {
    const {layer, detid, type, tableName} = ctx;

    console.log("ctx: ", ctx)
    function tbodyCreate(data) {
        let tbody = "";
        //console.log("data: ", data)
        if(data) data.forEach((item, i) => {
            let tr = /*html*/`
            <tr data-id="${item.id}">
                <td class="linenumber filetable-context" data-id="${item.id}" data-table="uplfiles">${i+1}</td>
                <td class="maskname editable" data-column="maskname"><div class="filenameBox"><div>${iconLinkCreate([item])}</div>&nbsp;<div class="fileName" contenteditable="true">${item.maskname}</div></div></td>
                <td class="prim editable" data-column="prim" contenteditable="true">${item.prim}</td>
            </tr>`;

            tbody += tr;
        });
        
        return `<tbody>${tbody}</tbody>`;
    }

    const mainframe = document.querySelector(layer);
    mainframe.innerHTML = "";
    const tableHeader = /*html*/`
        <thead class="headerSection">
            <tr class="colHeader">
                <td class="nomer">№</td>
                <td class="maskname" data-column="maskname">Имя файла</td>
                <td class="prim" data-column="prim">Примечание</td>
            </tr>
        </thead>
    `;
    
    loadFileList(ctx).then(data => {

        console.log("(loadFileList)Данные получены: ", data);

        const table = /*html*/`
            <table class="fileListTable" data-table="uplfiles" data-id="${detid}">
                ${tableHeader}
                ${tbodyCreate(data)}
            </table>
            <div class="addFileIconBox">
                <img src="./include/file_add.png">
                <input type="file" id="fileInput" style="display: none;">
            </div>
        `;

        const maintable = document.createRange().createContextualFragment(table);

        mainframe.append(maintable);
        txtEditor(mainframe);

        $.contextMenu('destroy', '.filetable-context');
        $.contextMenu({  //меню удаления
            selector: '.filetable-context',
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
                            }
                            saveData(data).then(dt => {
                                createFileTable(ctx);
                                state.mainTable();
                                replaceDelFile({id: this[0].dataset.id})                        
                            });
                        }
                        else alert('Отмена удаления');
                    }
                },
                sep1: '---------',
                quit: {
                    name: 'Выйти',
                    callback: function(key, options) {}
                }
            }
        });
        
        document.querySelector(".addFileIconBox img").addEventListener("click", () => {
            document.getElementById("fileInput").click();  // Программно вызываем окно выбора файла
        });

        document.getElementById("fileInput").addEventListener("change", (event) => {
            const files = event.target.files;
            if (files.length > 0) {
                sendFilesToServer(files, detid, type, tableName, () => {
                    createFileTable(ctx);
                    state.mainTable();
                });
            }
        });
    });
}

function sendFilesToServer(files, detid, type, tableName, reload) {
    const formData = new FormData();

    formData.append("detid", detid);
    formData.append("type", type);
    formData.append("tableName", tableName);
    
    // Добавляем файлы в FormData
    Array.from(files).forEach(file => {
        formData.append("files[]", file);
    });

    // Отправляем файлы через fetch
    fetch("./api/uloadFiles.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
        console.log("Файлы успешно загружены!");
        reload();
        } else {
        console.error("Ошибка загрузки файлов.");
        }
    })
    .catch(error => {
        console.error("Ошибка при отправке файлов:", error);
    });
}

function replaceDelFile({id} = {}) {
    const obj = {
        id: id,
    };
      
    return fetch('./api/transferFileToDeleteDir.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // Указываем JSON
        body: JSON.stringify(obj) // Преобразуем объект в JSON-строку
    })
    .then(res => res.json())
    .then(data => {
        return data;
    });
        
}
