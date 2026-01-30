import { dataTransfer } from "./dataTransfer.js";
import { state } from "./state.js";

export function getCotextProjectList(ctx = {}) {
	const {lineCount = 20, fl = 'txtSave', title = 'Переместить'} = ctx
	let blocks = {}, line = {}, j = 0;
	const izdList = document.querySelectorAll('.tree_item.menuitem');
	izdList.forEach((item, i) => {
		line[`lineName_${i}`] = {
			name: `${item.innerText}`, callback: function() {
				//newlocate('move', this[0].dataset.table, item.dataset.izdnomer, this[0].dataset.id, item.innerText)
                const data = {
                    table: this[0].dataset.table,
                    id: this[0].dataset.id,
                    column: 'detid',
                    content: item.dataset.izdnomer,
                    fl: fl,
                }
                if(confirm(`Перенести запись в раздел '${item.innerText}' ? `)) {
                    dataTransfer(data).then(dt => {
                        state.mainTable({scrollPos: 1});
                    });                    
                }
			}
		}
		if(i%lineCount == 0 && i > 0) {
			blocks[`block_${j++}`] = {name: `${(j-1)*lineCount+1} - ${i}`, items: line}
			line = {};
		}
	})
	blocks[`block_${j++}`] = {name: `${(j-1)*lineCount+1} - ${izdList.length}`, items: line}

	return {name: title, items: blocks};
}