import { advAPIResponse } from './types/Responses/AdvResponse';
import { BaseAxios } from './ajax';
import { GameLogger } from './utilities/GameLogger';
import { AdvEventManager } from './events/AdvEventManager';

export class AdvApi extends BaseAxios {
    protected static interceptorsConfigured = false;

    private static init() {
        BaseAxios.getInstance().interceptors.response.use(
            response => {
                if (response.status !== 200) {
                    GameLogger.addMessage('An Error Occured', true);
                    return Promise.reject(response);
                } else if (Array.isArray(response.data.gameMessage) === true) {
                    GameLogger.addMessages(response.data.gameMessage, true);
                    GameLogger.logMessages();
                    return Promise.resolve(response);
                } else if (
                    typeof response.data.errorGameMessage !== 'undefined'
                ) {
                    GameLogger.addMessage(response.data.errorGameMessage, true);
                    return Promise.reject(response.data.gameMessage);
                }

                if (Array.isArray(response.data.gameEvents)) {
                    AdvEventManager.notify(response.data.gameEvents);
                }

                if (
                    Array.isArray(response.data.levelUp) &&
                    response.data.levelUp.length > 0
                ) {
                    GameLogger.addMessage('Level Up!', false);
                    response.data.levelUp.forEach(skill => {
                        GameLogger.addMessage(
                            `You have gained a level in ${skill.name}!`,
                            false,
                        );
                    });
                }

                return response;
            },
            error => {
                if (Array.isArray(error.response.data.gameMessage) === true) {
                    GameLogger.addMessages(
                        error.response.data.gameMessage,
                        true,
                    );
                    GameLogger.logMessages();
                }

                return Promise.reject(error);
            },
        );
        this.interceptorsConfigured = true;
    }

    public static async get<T = advAPIResponse>(url: string): Promise<T> {
        if (this.interceptorsConfigured === false) this.init();

        return BaseAxios.get<T>(url);
    }

    public static async post<T = advAPIResponse>(
        url: string,
        data: object = {},
    ): Promise<T> {
        if (this.interceptorsConfigured === false) this.init();

        return BaseAxios.post<T>(url, data);
    }
}
