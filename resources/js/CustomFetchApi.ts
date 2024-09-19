import { BaseAxios } from './ajax';
import { addModuleTester } from './devtools/ModuleTester';

export class CustomFetchApi extends BaseAxios {
    public static async get<T>(url: string): Promise<T> {
        return BaseAxios.get<T>(url);
    }

    public static async post<T, K extends object = object>(
        url: string,
        data: K,
    ): Promise<T> {
        return BaseAxios.post<T>(url, data);
    }
}

addModuleTester(CustomFetchApi, 'CustomFetchApi');
