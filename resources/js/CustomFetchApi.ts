import { isAxiosError } from 'axios';
import { BaseAxios } from './ajax';
import { addModuleTester } from './devtools/ModuleTester';
import { GameLogger } from './utilities/GameLogger';

export class CustomFetchApi extends BaseAxios {
    protected static interceptorsConfigured = false;

    private static init() {
        BaseAxios.getInstance().interceptors.response.use(
            response => {
                return response;
            },
            error => {
                if (isAxiosError(error)) {
                    let text;
                    if (
                        error.response?.status != null &&
                        error.response?.status >= 500 &&
                        error.response?.status <= 511
                    ) {
                        text = 'An error has occured. Please try again later.';
                    } else if (error?.response?.data.message != null) {
                        text = error.response.data.message;
                    }

                    GameLogger.addMessage(
                        {
                            text,
                            type: 'error',
                        },
                        true,
                    );
                }
                return Promise.reject(error);
            },
        );

        this.interceptorsConfigured = true;
    }

    public static async get<T>(url: string): Promise<T> {
        if (this.interceptorsConfigured === false) this.init();
        return BaseAxios.get<T>(url);
    }

    public static async post<T, K extends object = object>(
        url: string,
        data: K,
    ): Promise<T> {
        if (this.interceptorsConfigured === false) this.init();

        return BaseAxios.post<T>(url, data);
    }
}

addModuleTester(CustomFetchApi, 'CustomFetchApi');
