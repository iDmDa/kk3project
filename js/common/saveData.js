export function saveData({table, column, id, content} = {}) {
    const obj = {
        table: table,
        column: column,
        id: id,
        content: content,
    };
      
    return fetch('./api/txtSave.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // Указываем JSON
        body: JSON.stringify(obj) // Преобразуем объект в JSON-строку
    })
    .then(res => res.json())
    .then(data => {
        return data;
    });
}