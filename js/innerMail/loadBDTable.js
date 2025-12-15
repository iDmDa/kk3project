export function loadDBTable(izdelieid, page) {
    const obj = {
        izdelieid: izdelieid,
        page: page,
    };
      
    return fetch('./api/innerMail.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // Указываем JSON
        body: JSON.stringify(obj) // Преобразуем объект в JSON-строку
    })
    .then(res => res.json())
    .then(data => {
        //console.log(data)
        return data;
    });
        
}
