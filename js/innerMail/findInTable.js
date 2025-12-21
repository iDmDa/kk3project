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
            <input type="search" style="width: 600px;">
            <div class="mailQuestion" style="margin-left: 5px;" data-title="${info}"><img src="include/question.png">
            </div>
    `
    const mainframe = document.querySelector(layer);
    const resultBlock = document.createRange().createContextualFragment(content);

    mainframe.append(resultBlock);

}