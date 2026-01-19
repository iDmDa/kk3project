// export function varControlEvt({varName, callback} = {}) {
//     Object.defineProperty(window, `${varName}`, {
//         get() { return this._value; },
//         set(val) {
//             this._value = val;
//             //console.log("Плагин реагирует на изменение:", val);

//             try {
//                 callback(val);
//             }
//             catch {}
//         }
//     });       
// }

export function varControlEvt({ varName, callback } = {}) {
    window.__varStore ??= {};
    window.__varListeners ??= {};

    if (!window.__varStore.hasOwnProperty(varName)) {
        window.__varStore[varName] = window[varName];
        window.__varListeners[varName] = [];

        Object.defineProperty(window, varName, {
            configurable: true,
            get() {
                return window.__varStore[varName];
            },
            set(val) {
                window.__varStore[varName] = val;
                window.__varListeners[varName]
                .forEach(fn => fn(val));
            }
        });
    }

    if (typeof callback === 'function') {
        window.__varListeners[varName].push(callback);
    }
}

