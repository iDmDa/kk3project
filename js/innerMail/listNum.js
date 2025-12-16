import { loadInnerMail } from "./script.js";

    export function listNum(allPages, izdelieid, activePage) {
        if(activePage == -1) activePage = allPages - 1;
        let pages = "";
        for (let i = 0; i < allPages; i++) {
            const page = /*html*/`
                <div class="menuitem ${activePage == i ? "menuactive": ""}">${i+1}</div>
            `;
            pages += page;
        }

        const fragment = document.createRange().createContextualFragment(/*html*/`
            <div class="pagesBlock">${pages}</div>
        `);

        const pageBlock = fragment.querySelector(".pagesBlock");
        pageBlock.addEventListener("click", (e) => {
            console.log("listy", e.target.innerText);
            loadInnerMail(izdelieid, e.target.innerText - 1);
        })

        return fragment;
    }