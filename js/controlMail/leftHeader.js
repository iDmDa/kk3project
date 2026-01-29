export function leftHeader() {
    const table = document.querySelector('.controlMail');
    const header = table.querySelector('.headerlinenumber');
    const numbers = table.querySelectorAll('.linenumber');
    const tree = document.querySelectorAll('#tree_layer .tree_item');

    const treeList = {};
    tree.forEach(item => treeList[item.dataset.izdnomer] = item.innerText);

    const td = (name) => {
        const td = document.createElement('td');
        td.className = 'leftHeader';
        td.textContent = name;
        return td;
    };

    header.after(td('Изделие'));

    numbers.forEach(item => {
        const detid = item.closest('tr').dataset.detid;
        item.after(td(treeList[detid]));
    });
}