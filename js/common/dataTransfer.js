export function dataTransfer(obj) {
    const sendObj = {...obj};
    
    delete sendObj.fl; //Удаление полей, которые не надо отправлять
    // if (sendObj.reload && typeof sendObj.reload === "function") {
    //     delete sendObj.reload();
    // }
      
    return fetch(`./api/${obj.fl}.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }, // Указываем JSON
        body: JSON.stringify(sendObj) // Преобразуем объект в JSON-строку
    })
    .then(res => res.json())
    .then(data => {
        return data;
    });
}