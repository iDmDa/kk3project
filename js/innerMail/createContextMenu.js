import { dataTransfer } from "../commonTableFnc/dataTransfer.js";
import { state } from "../commonTableFnc/state.js";

export function createContextMenu() {
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