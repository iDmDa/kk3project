import { moveResize } from "./moveResize.js";
import { state } from "./state.js";

export function createWindow ({windowClass = "tmpWin", headerName = "Список файлов", content = "", contentLoader} = {}) {
    document.querySelector(`.${windowClass}`)?.remove();
    const window = /*html*/ `
        <div class="modalWindowBox ${windowClass}" style="height: 270px">
            <div class="tmp_box">

                <div class="tmp_header">
                    <div class="tmp_window_hide">–</div>
                    <div class="tmp_header_txtbox">${headerName}</div>
                    <div class="tmp_close">&#x2715;</div>
                </div>

                <div class="tmp_content">
                    ${content}
                </div>

                <div class="tmp_footer"></div>

            </div>
            <div class="tmp_resizer"></div>
        </div>
    `;
    const fragment = state.createHTML(window);
    const body = document.querySelector("body");
    body.append(fragment);
    moveResize({slideLayer: body, windowClass: `.${windowClass}`});

    contentLoader();
}