import { advAPIResponse } from './types/Responses/AdvResponse';
import { BaseAxios } from "./ajax";
import { GameLogger } from './utilities/GameLogger';

export class AdvApi extends BaseAxios {

    protected static interceptorsConfigured = false;

    private static init() {
        BaseAxios.getInstance().interceptors.response.use(response => {

            if(response.status !== 200) {
                GameLogger.addMessage("An Error Occured", true);
                return Promise.reject(response);
            }

            else if (typeof response.data.gameMessage !== "undefined") {
                GameLogger.addMessage(response.data.gameMessage, true);
                return Promise.resolve(response);
            } else if (typeof response.data.errorGameMessage !== "undefined") {
                GameLogger.addMessage(response.data.errorGameMessage, true);
                return Promise.reject(response.data.gameMessage);
            }   

            return response;
        })
        this.interceptorsConfigured = true;
    }

    public static async get<T = advAPIResponse>(url: string): Promise<T> {
        if(this.interceptorsConfigured === false) this.init();

        return BaseAxios.get<T>(url);
    }

    public static async post<T = advAPIResponse>(url: string, data: Object): Promise<T> {
        if(this.interceptorsConfigured === false) this.init();

        return BaseAxios.post<T>(url, data);
    }
}


