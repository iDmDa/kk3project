import { state } from "./state.js";

export function moveResize({slideLayer, windowClass, headerHeight = 29} = {}) {
    const win = slideLayer.querySelector(windowClass);
    const field = slideLayer;
    //console.log("win", windowClass);

    const header = win.querySelector('.tmp_header');
    const tmp_resizer = win.querySelector(".tmp_resizer");
    const tmp_content = win.querySelector(".tmp_content");
    const tmp_footer = win.querySelector(".tmp_footer");

    let offsetX = 0;
    let offsetY = 0;
    let dragging = false, resize = false;
    let startX, startY, startW, startH;

    header.addEventListener('mousedown', e => {
        dragging = true;
        offsetX = e.clientX - win.offsetLeft; //e.clientX - координата мышки относительно окна
        offsetY = e.clientY - win.offsetTop;  //win.offsetLeft - координата блока относительно родительского блока
        header.style.cursor = 'grabbing';
    });

    tmp_resizer.addEventListener('mousedown', e => {
        resize = true;
        startX = e.clientX;
        startY = e.clientY;
        startW = win.offsetWidth; //win.offsetWidth - ширина элемента, но без margin
        startH = win.offsetHeight;
        e.stopPropagation();
    });

    field.addEventListener('mousemove', e => {
        if (dragging) {
            win.style.left = e.clientX - offsetX + 'px';
            win.style.top = e.clientY - offsetY + 'px';
        } else
        if (resize) {
            win.style.left = win.offsetLeft + 'px'; //закрепляем левую границу блока
            win.style.top = win.offsetTop + 'px';
            win.style.width = `${Math.max(120, startW + (e.clientX - startX))}px`;
            win.style.height = `${Math.max(100, startH + (e.clientY - startY))}px`;
            
        }
    });

    field.addEventListener('mouseup', e => {
        dragging = resize = false;
        header.style.cursor = 'grab';
    });

    header.querySelector(".tmp_close").addEventListener("click", () => {
        win.remove();
        state.closeFileWindow();
    })

    header.querySelector(".tmp_window_hide").addEventListener("click", () => {
        //header.querySelector(".tmp_window_hide").classList.toggle("tmp_hide");
        tmp_content.classList.toggle("tmp_hide");
        tmp_resizer.classList.toggle("tmp_hide");
        tmp_footer.classList.toggle("tmp_hide");
        if(tmp_content.classList.contains("tmp_hide")) {
            header.querySelector(".tmp_window_hide").innerText = "+";
            win.dataset.height = win.offsetHeight;
            win.style.height = headerHeight + 'px';
        }
        else {
            header.querySelector(".tmp_window_hide").innerText = "–";
            win.style.height = win.dataset.height + 'px';
        }
    })
}