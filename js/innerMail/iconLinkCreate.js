export function iconLinkCreate(obj) {
    if(!obj || obj.length === 0) return "";
    let icons = "bmp, cdw, doc, docx, gif, jpg, mp3, pdf, png, tif, tiff, txt, xls, xlsx, rar, zip";
    let result = "";
    [...obj].forEach(link => {
        const nameArr = link.filename.split(".");
        let ext = nameArr[nameArr.length - 1];
        if(icons.indexOf(ext.toLowerCase()) < 0) ext = "unknow";
        const text = /*html*/`
            <a href="../projectdata/${link.local_path}/${link.prefix}_${link.filename}" target="_blank">
                <img src="./include/ico/${ext}.png" title="${link.maskname}">
            </a>
        `
        result += text;
    });
    return result;
}