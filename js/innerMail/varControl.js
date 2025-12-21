export function varControlEvt({varName, action} = {}) {
    Object.defineProperty(window, `${varName}`, {
        get() { return this._value; },
        set(val) {
            this._value = val;
            //console.log("Плагин реагирует на изменение:", val);

            try {
                action(val);
            }
            catch {}
        }
    });       
}

