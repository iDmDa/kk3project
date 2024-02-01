function createIcons(resultArray, scanArr) {
    let td;
    let extList = "bmp, doc, docx, gif, jpg, mp3, pdf, png, tif, tiff, txt, xls, xlsx";
    for(let i = 0; i < resultArray.length; i++) {
        for(let j = 0; j < scanArr.length; j++) {
            if(resultArray[i]['type'] == scanArr[j][0]) td = document.getElementById(`${resultArray[i]['detid']}${scanArr[j][1]}`);
        }
        let filetype = resultArray[i]["filename"].split(".").reverse()[0].toLowerCase();
        let a = document.createElement("a");
        let img = document.createElement("img");
        let textNode = document.createTextNode(" ");
        a.href = `/projectdata/${resultArray[i]["tabname"]}/${resultArray[i]["prefix"]}_${resultArray[i]["filename"]}`;
        a.target = "_blank";
        extList.indexOf(filetype) < 0 ? img.src = `include/ico/unknow.png` : img.src = `include/ico/${filetype}.png`;
        img.title = `${resultArray[i]["filename"]}`;
        a.appendChild(img);
        td.appendChild(a);
        td.appendChild(textNode);
    }

    let colSelector = "";
    for(let j = 0; j < scanArr.length; j++) {
        colSelector += `[id$="${scanArr[j][1]}"], `;
    }
    colSelector = colSelector.substring(0, colSelector.length-2);
    td = document.querySelectorAll(`${colSelector}`);
    td.forEach(item => {
        let img = document.createElement("img");
        img.src = `include/new window.png`;
        img.style.float = "right";
        img.classList.add("button_field");
        if(zamok == 0) img.style.display = "none";
        item.appendChild(img);
    });
}