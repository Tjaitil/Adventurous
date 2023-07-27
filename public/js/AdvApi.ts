import { advAPIResponse } from './types/responses/AdvResponse';
import { checkResponse } from "./ajax.js";
import { gameLogger } from './utilities/gameLogger.js';

type responseType<T> = T extends advAPIResponse ? T : T;
export class AdvApi {
    private static route = "/api";

    private static fetchInstance<T extends {}>(method: 'PUT' | 'GET' | 'POST', url: string, data?: Object): Promise<T> {

        const requestInfo: RequestInit = {
            method: method,
            headers: { "Content-type": "application/json" },
        }
        if (data !== undefined) requestInfo.body = JSON.stringify(data);

        return fetch(this.route + url, requestInfo)
            .then((res) => {
                if (!res.ok) {
                    return res.json().then((data) => {
                        return Promise.reject(data);
                    });
                }
                return res.json();
            })
            .then((data: T) => {
                checkResponse(data);
                return data;
            })
            .catch((errorMessage) => {
                checkResponse(errorMessage);
                return Promise.reject(errorMessage);
            });
    }

    public static get<T = advAPIResponse>(url: string): Promise<responseType<T>> {
        return this.fetchInstance<responseType<T>>('GET', url);
    }


    public static post<T = advAPIResponse>(url: string, data: Object): Promise<responseType<T>> {

        return this.fetchInstance<responseType<T>>('POST', url, data);
    }
}

(<any>window).AdvApi = AdvApi;
