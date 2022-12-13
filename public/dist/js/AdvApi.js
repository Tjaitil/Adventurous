import { checkResponse } from "./ajax.js";
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
            .then((res) => res.json())
            .then((data) => {
            checkResponse(data);
            return data;
        })
            .catch((error) => {
            checkResponse(error);
            throw new Error(error);
        });
    }
    static get(url) {
        return this.fetchInstance('GET', url);
    }
    static post(url, data) {
        return fetch(this.route + url, {
            method: "POST",
            headers: { "Content-type": "application/json" },
            body: JSON.stringify(data),
        }).then((res) => res.json()).then((data) => {
            checkResponse(data);
            return data;
        })
            .catch((error) => {
            checkResponse(error);
            throw new Error(error);
        });
    }
}
window.AdvApi = AdvApi;
