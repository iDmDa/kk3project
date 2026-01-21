import { editFunctions } from "./editFunctions.js";
import { state } from "./state.js";
import { varControlEvt } from "./varControl.js";

export function varIndexCtrl() {

    state.openStatus = window.openStatus;
    state.tabInfo = {};

    varControlEvt({varName: "openStatus", callback: (val) => {
        editFunctions({openStatus: val, ...state.tabInfo});
        state.openStatus = val;
    }});
}