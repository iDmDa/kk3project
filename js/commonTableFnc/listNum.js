export function listNum({allPages, activePage, clkEvt} = {}) {
    if(activePage == -1) activePage = allPages - 1;
    let pages = "";
    for (let i = 0; i < allPages; i++) {
        const page = /*html*/`
            <div class="menuitem button ${activePage == i ? "menuactive": ""}">${i+1}</div>
        `;
        pages += page;
    }

    const fragment = document.createRange().createContextualFragment(/*html*/`
        <div class="pagesBlock">${pages}</div>
    `);

    const pageBlock = fragment.querySelector(".pagesBlock");
    pageBlock.addEventListener("click", (e) => {
        if(e.target.classList.contains("button")) clkEvt(e.target.innerText);
    })

    return fragment;
}