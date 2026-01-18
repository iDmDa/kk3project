export function varControlEvt({varName, callback} = {}) {
    Object.defineProperty(window, `${varName}`, {
        get() { return this._value; },
        set(val) {
            this._value = val;
            //console.log("Плагин реагирует на изменение:", val);

            try {
                callback(val);
            }
            catch {}
        }
    });       
}

