import { contextMenuLineSelected } from "../commonTableFnc/contextMenuLineSelected.js";
import { dataTransfer } from "../commonTableFnc/dataTransfer.js";
import { getCotextProjectList } from "../commonTableFnc/getCotextProjectList.js";
import { state } from "../commonTableFnc/state.js";

export function createContextMenu(contextName) {
    $.contextMenu('destroy', `.${contextName}`);
    $.contextMenu({  //меню удаления
        selector: `.${contextName}`,
        events: contextMenuLineSelected(),
        items: {
            delete: {
                name: 'Удалить',
                callback: function() {
                    if (prompt("Для подтверждения удаления введите '1'", 'Введите: 1') == true) {
                        const data = {
                            table: this[0].dataset.table,
                            id: this[0].dataset.id,
                            column: 'hide',
                            content: 1,
                            fl: 'txtSave',
                        }
                        dataTransfer(data).then(dt => {
                            state.mainTable({scrollPos: 1});
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
                        id: this[0].dataset.id,
                        column: 'hide',
                        content: 0,
                        fl: "txtSave",
                    }
                    dataTransfer(data).then(dt => {
                        state.mainTable({scrollPos: 1});
                    });
                }
            },
            moveToOtherTask: getCotextProjectList(),
            sep1: '---------',
            quit: {
                name: 'Выйти',
                callback: () => {}
            }
        }
    });
}