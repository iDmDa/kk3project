const range = document.createRange();

export const state = {
    openStatus: 0,
    range,
    createHTML: (content) => range.createContextualFragment(content),
    loadHtml: (layer, hTxt) => {
        const div = document.querySelector(layer);
        const html = state.createHTML(hTxt);
        div.replaceChildren(html);
    },
    appendHtml: (layer, hTxt, Evt = () => {}) => {
        const div = document.querySelector(layer);
        const html = state.createHTML(hTxt);
        div.append(html);
        Evt();
    },
};