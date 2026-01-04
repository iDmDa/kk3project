import { saveData } from "./saveData.js";

export function txtEditor(el) { 
    let defaultValue = "";
    let editElement;

    el.addEventListener("click", (e) => { // Сохранение текста до изменения
        if(editElement !== e.target) {
            editElement = e.target;
            if(editElement.contentEditable === "true") {
                defaultValue = editElement.innerHTML;
            }            
        }
    })

    el.addEventListener('focusout', (e) => {
        console.log('Смена фокуса с элемента: ', e.target, e.target.innerHTML);
        console.log('Элемент: ', e.target.closest("table").dataset.table, e.target.dataset.column, e.target.closest("tr").dataset.id);
        const data = {
            table: e.target.closest("table").dataset.table,
            column: e.target.dataset.column,
            id: e.target.closest("tr").dataset.id,
            content: e.target.innerHTML,
        }
        if(defaultValue !== e.target.innerHTML) saveData(data);
    });

    el.addEventListener("keydown", e => {
        if (e.key === 'Enter111') { //Не верно работает, багуется при backspace
            e.preventDefault();

            const sel = window.getSelection();
            const range = sel.getRangeAt(0);

            // удаляем выделение
            range.deleteContents();

            // вставляем <br>
            const br = document.createElement('br');
            const text = document.createTextNode('\u200B');
            range.insertNode(text);
            range.insertNode(br);

            // Ставим курсор после пробела
            range.setStart(text, 0);
            range.collapse(true);

            sel.removeAllRanges();
            sel.addRange(range);
        }

		if (e.key === 'Enter') {//замена <div> на <br> при вводе enter
            e.preventDefault();
			document.execCommand('insertText', false, '\n');
		}

        if(e.key === 'Escape') { //Возврат значения до начала редактирования
            e.target.innerHTML = defaultValue;
        }
    });
}

