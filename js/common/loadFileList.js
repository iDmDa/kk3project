export function loadFileList({detid, type} = {}) {
    const obj = {
        detid: detid,
        type: type,
    };
      
    return fetch('./api/getFileList.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // Указываем JSON
        body: JSON.stringify(obj) // Преобразуем объект в JSON-строку
    })
    .then(res => res.json())
    .then(data => {
        return data;
    });
        
}
