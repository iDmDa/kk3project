import { state } from "../common/state.js";

export function findInTable({layer} = {}) {
    const info = 
/*html*/`Поиск по диапазону дат:
- указать диапазон между знаками # #;
- после можно указать поисковое слово.

Например:
#10.01.2022-16.03.2022#
Будут выведены все строки между указанными 
датами включая 10 и 16 число.

#10.01.2022-16.03.2022# изделие
В указанном диапазоне будет задан поиск по слову 'изделие'.`;

    const content = /*html*/`
        <span>Найти:</span>
            <input class="filter" type="search" style="width: 600px;">
            <div class="mailQuestion" style="margin-left: 5px;" data-title="${info}"><img src="include/question.png">
            </div>
    `
    const mainframe = document.querySelector(layer);
    const resultBlock = document.createRange().createContextualFragment(content);

    mainframe.append(resultBlock);

    mainframe.addEventListener("change", (e) => {
        if(e.target.classList.contains("filter")) {
            state.mainTable({filter: e.target.value, callback: () => findSelector(e.target.value)});
        }
    })
}

function findSelector(find) {
    // Если текст имеет символ #, то очищаем его
    if ((find.match(/#/g) || []).length == 2) {
        find = find.split("#")[2].trim();
    }
    
    if (find == "") return;

    // Находим все ячейки таблицы
    const findSelect = document.querySelectorAll('table tbody td');

    findSelect.forEach(item => {
        // Получаем текстовое содержимое ячейки
        let text = item.textContent || item.innerText;

        // Проверяем, есть ли нужный текст с игнорированием регистра
        if (text.match(new RegExp(find, 'gi'))) {
            // Заменяем текст с подсветкой
            let highlightedText = item.innerHTML.replace(
                new RegExp(`(${find})`, 'gi'), 
                '<u>$1</u>'
            );
            item.innerHTML = highlightedText;
        }
    });
}