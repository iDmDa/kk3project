export function findSelector(find) {
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