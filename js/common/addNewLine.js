export function addNewLine({table, id, hide, reload} = {}) {
    const obj = {
        table: table,
        id: id,
        hide: hide,
    };
      
    return fetch('./api/addNewLine.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // Указываем JSON
        body: JSON.stringify(obj) // Преобразуем объект в JSON-строку
    })
    .then(res => res.json())
    .then(data => {
        if (reload && typeof reload === "function") {
            reload();  // Передаем данные в колбэк
        }
        return data;
    });
}