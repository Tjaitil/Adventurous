import { advAPIResponse } from "./types/Responses/AdvResponse";
import { GameLogger } from "./utilities/GameLogger";
import axios, {Axios} from 'axios';


export class BaseAxios {
    private static route = window.location.origin;
    
    private static AxiosInstance: Axios;

    public static getInstance() {
        if(!this.AxiosInstance) this.createAxiosInstance();

        return this.AxiosInstance;
    }

    private static createAxiosInstance() {
        this.AxiosInstance = axios.create({
            baseURL: this.route,
            headers: { 
                "Content-type": "application/json",
            },
        });
    
        this.AxiosInstance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }

    public static async get<T = advAPIResponse>(url: string): Promise<T> {
        if(!this.AxiosInstance) this.createAxiosInstance();


        return await this.AxiosInstance.get<T>(url).then(async (response) => response.data);

    }

    public static async post<T = advAPIResponse>(url: string, data: Object): Promise<T> {
        if(!this.AxiosInstance) this.createAxiosInstance();
        
        return await this.AxiosInstance.post<T>(url, data).then(async (response) => response.data);
    }
}



// scriptLoader.loadScript(["GameLogger"], "utility");

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
                GameLogger.addMessage("ERROR!:");
                console.log(this.responseText);
                GameLogger.logMessages();
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
                // GameLogger.addMessage("ERROR Something unexpected happened!");
                // GameLogger.logMessages();
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
export function checkResponse(response: advAPIResponse) {
    // if (typeof response.levelUp !== undefined &&
    //     Object.keys(response.levelUp ?? {}).length > 0) {
    //     LevelManager.update(response.levelUp);
    // }
    if (typeof response.gameMessage !== "undefined") {
        GameLogger.addMessage(response.gameMessage);
        GameLogger.logMessages();
    } else if (typeof response.errorGameMessage !== "undefined") {
        GameLogger.addMessage(response.errorGameMessage);
        GameLogger.logMessages();
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

