class IzvTableGenerator {

}

function izvLoad (izv, tab_id) {
    let data = new FormData();
    data.append(izv, "value");
    data.append("tab_id", tab_id)

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'izveshenie.php', true);
    xhr.send(data);
    xhr.onload = function () {
        let resp = xhr.response; //Результат запроса
        resultArray = JSON.parse(resp);
        console.log(resultArray);
    }

	xhr.onloadend = function(event) {
        zamok == 1 ? open_edit() : close_edit();
		$(".dateinput").mask("99.99.9999", {placeholder: "дд.мм.гггг" });
    	console.log("(xhrLoad)Загрузка завершена");
  	}
}