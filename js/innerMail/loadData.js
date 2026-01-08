export function loadData(ctx = {}) {
    const {izdelieid, page} = ctx
    const obj = {
        izdelieid: izdelieid,
        page: page,
    };
      
    return fetch('./api/innerMail.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // Указываем JSON
        body: JSON.stringify(ctx) // Преобразуем объект в JSON-строку
    })
    .then(res => res.json())
    .then(data => {
        return data;
    });
        
}
