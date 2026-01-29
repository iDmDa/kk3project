export function contextMenuLineSelected() {
    return {
        show: function(opt) {
            const trigger = this[0].closest("tr");
            trigger.classList.add('lineSelected');
        },
        hide: function(opt) {
            this[0].closest("tr").classList.remove('lineSelected');
        }
    }
}