import { newLevel } from "./levelup.js";
import { gameLogger } from "./utilities/gameLogger.js";

// scriptLoader.loadScript(["gameLogger"], "utility");

function validJSON(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
function checkError(responseText) {
    let errorWords = ["ERROR", "error", "notice", "Exception", "exception", "Trace", "trace", "Warning"];
    let match = false;
    for (const i of errorWords) {
        if (responseText.includes(i)) {
            console.log("Error key found at ", i);
            match = true;
            break;
        }
    }
    if (match === true) {
        return true;
    } else {
        return false;
    }
}

export async function ajaxG(data, callback, log = true) {
    let ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            let responseText = JSON.parse(this.responseText);
            checkResponse(responseText);
            if (checkError(this.responseText)) {
                callback([false, responseText]);
            } else {
                callback([true, responseText]);
            }
        }
    };
    ajaxRequest.open("GET", "handlers/handler_g.php?" + data);
    ajaxRequest.send();
}
export async function ajaxJS(data, callback, log = true, file: string = "handler_js") {
    let ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            let responseText = JSON.parse(this.responseText);
            checkResponse(responseText);
            if (checkError(this.responseText)) {
                callback([false, responseText]);
                gameLogger.addMessage("ERROR!:");
                console.log(this.responseText);
                gameLogger.logMessages();
            } else {
                callback([true, responseText]);
            }
        } else {
            console.log(this.responseText);
        }
    };
    ajaxRequest.open("GET", "handlers/" + file + ".php?" + data);
    ajaxRequest.send();
}
export async function ajaxP(data, callback, log = true) {
    // const response = await fetch("./handlers/handler_p.php", {
    //     method: "POST",
    //     headers: { "Content-type": "application/json" },
    //     body: JSON.stringify(data),
    // })
    //     .then((res) => res.json())
    //     .then((res) => checkResponse(res))
    //     .catch((error) => checkResponse(error));

    // return response;
    let ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            let responseText = JSON.parse(this.responseText);
            console.log(this.responseText);
            checkResponse(responseText);
            if (checkError(this.responseText)) {
                callback([false, responseText]);
                // gameLogger.addMessage("ERROR Something unexpected happened!");
                // gameLogger.logMessages();
            } else {
                callback([true, responseText]);
            }
        } else {
            console.log(this.responseText);
        }
    };
    ajaxRequest.open("POST", "handlers/handler_p.php");
    ajaxRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajaxRequest.send(data);
}
export function checkResponse(responseText) {
    if (typeof responseText.levelUP !== "undefined" && Object.keys(responseText.levelUP).length > 0) {
        newLevel.update(responseText.levelUP);
    }
    if (typeof responseText.gameMessages !== "undefined") {
        gameLogger.addMessage(responseText.gameMessages);
        gameLogger.logMessages();
    }
}




// const response = await fetch("./handlers/handler_p.php", {
//     method: "POST",
//     headers: { "Content-type": "application/json" },
//     body: JSON.stringify(data),
// })
//     .then((res) => res.json())
//     .then((res) => checkResponse(res))
//     .catch((error) => checkResponse(error));

// return response;

