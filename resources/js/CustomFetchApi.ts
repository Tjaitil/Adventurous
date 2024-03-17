import { BaseAxios } from './ajax';

export class CustomFetchApi extends BaseAxios {
    public static async get<T>(url: string): Promise<T> {
        return BaseAxios.get<T>(url);
    }

    public static async post<T>(url: string, data: Object): Promise<T> {
        return BaseAxios.post<T>(url, data);
    }
}

(<any>window).AdvApi = CustomFetchApi;
