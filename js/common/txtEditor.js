import { saveData } from "./saveData.js";

export function txtEditor(el) { //el точка привязки событий
    //Сохранение изменений текста в полях contentEditable
    let defaultValue = "";
    let editElement;

    el.addEventListener("click", (e) => { // Сохранение текста до изменения
        if(e.target.contentEditable === "true") {
            if(editElement !== e.target) {
                editElement = e.target;
                if(editElement.contentEditable === "true") {
                    defaultValue = editElement.innerHTML;
                }            
            }           
        }
    })

    el.addEventListener('focusout', (e) => {
        if(e.target.contentEditable === "true" || e.target.classList.contains("dateinput")) {
            //console.log("блок сохранения: ", e.target);
            const data = {
                table: e.target.closest("table").dataset.table,
                column: e.target.closest("td").dataset.column,
                id: e.target.closest("tr").dataset.id,
                content: e.target.innerHTML,
            }
            if(defaultValue !== e.target.innerHTML) saveData(data);
            if(e.target.classList.contains("dateinput") && window.openStatus == "1") {
                data.content = e.target.value;
                saveData(data);
                console.log("sv: ", e.target.value, data, window.openStatus);
            }            
        }

    });

    el.addEventListener("keydown", e => {
        if(e.target.contentEditable === "true") {
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
        }
    });
}

