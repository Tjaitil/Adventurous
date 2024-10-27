import axios, { AxiosResponse } from 'axios';
import { errorInterceptor, hasResponseKey } from './ajax';
import { addModuleTester } from './devtools/ModuleTester';
import { GameLogger } from './utilities/GameLogger';

export class CustomFetchApi {
    private static init() {
        axios.interceptors.response.use(
            response => {
                if (hasResponseKey(response)) {
                    GameLogger.addMessages(response.data.logs, true);
                }
                return response;
            },
            error => {
                errorInterceptor(error);
                return Promise.reject(error);
            },
        );

        return axios;
    }

    public static async get<T>(url: string): Promise<AxiosResponse<T>> {
        return this.init().get<T>(url);
    }

    public static async post<T, K extends object = object>(
        url: string,
        data: K,
    ): Promise<AxiosResponse<T>> {
        return this.init().post<T>(url, data);
    }
}

addModuleTester(CustomFetchApi, 'CustomFetchApi');
