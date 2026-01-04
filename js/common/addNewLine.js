export function addNewLine({table, id, hide, callback} = {}) {
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
        if (callback && typeof callback === "function") {
            callback();  // Передаем данные в колбэк
        }
        return data;
    });
}