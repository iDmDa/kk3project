const range = document.createRange();

export const state = {
    openStatus: 0,
    range,
    createHTML: (content) => range.createContextualFragment(content),
};