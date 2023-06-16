import { checkResponse } from "./ajax.js";
import { gameLogger } from './utilities/gameLogger.js';
export class AdvApi {
    static route = "/api";
    static fetchInstance(method, url, data) {
        const requestInfo = {
            method: method,
            headers: { "Content-type": "application/json" },
        };
        if (data !== undefined)
            requestInfo.body = JSON.stringify(data);
        return fetch(this.route + url, requestInfo)
            .then((res) => {
            if (!res.ok) {
                return res.json().then((data) => {
                    return Promise.reject(new Error(data.message));
                });
            }
            return res.json();
        })
            .then((data) => {
            checkResponse(data);
            return data;
        })
            .catch((error) => {
            console.log(error);
            gameLogger.addMessage(error, true);
            checkResponse(error);
            return Promise.reject(new Error(error));
        });
    }
    static get(url) {
        return this.fetchInstance('GET', url);
    }
    static post(url, data) {
        return this.fetchInstance('POST', url, data);
    }
}
window.AdvApi = AdvApi;
