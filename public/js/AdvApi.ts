import { advAPIResponse } from './types/Responses/AdvResponse';
import { checkResponse } from "./ajax.js";

export class AdvApi {
    private static route = "/api";

    private static fetchInstance<T extends {}>(method: 'PUT' | 'GET' | 'POST', url: string, data?: Object): Promise<advAPIResponse<T>> {

        const requestInfo: RequestInit = {
            method: method,
            headers: { "Content-type": "application/json" },
        }
        if (data !== undefined) requestInfo.body = JSON.stringify(data);

        return fetch(this.route + url, requestInfo)
            .then((res) => res.json())
            .then((data: advAPIResponse<T>) => {
                checkResponse(data);
                return data;
            })
            .catch((error) => {
                checkResponse(error);
                throw new Error(error);
            });
    }

    public static get<T extends {}>(url: string,): Promise<advAPIResponse<T>> {
        return this.fetchInstance<T>('GET', url);
    }

    public static post<T extends {}>(url: string, data: Object): Promise<advAPIResponse<T>> {
        return fetch(this.route + url, {
            method: "POST",
            headers: { "Content-type": "application/json" },
            body: JSON.stringify(data),
        }).then((res) => res.json()).then((data: advAPIResponse<T>) => {
            checkResponse(data);
            return data;
        })
            .catch((error) => {
                checkResponse(error);
                throw new Error(error);
            })
    }
}

(<any>window).AdvApi = AdvApi;
