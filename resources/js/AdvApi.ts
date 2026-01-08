import type { advAPIResponse } from './types/Responses/AdvResponse';
import { BaseAxios, errorInterceptor, isAdvResponse } from './ajax';
import { GameLogger } from './utilities/GameLogger';

export class AdvApi extends BaseAxios {
  protected static interceptorsConfigured = false;

  private static init() {
    BaseAxios.getInstance().interceptors.response.use(
      response => {
        if (isAdvResponse(response)) {
          GameLogger.addMessages(response.data.logs, true);
        }

        return response;
      },
      error => {
        errorInterceptor(error);

        return Promise.reject(error);
      },
    );
    this.interceptorsConfigured = true;
  }

  public static async get<T = advAPIResponse>(url: string): Promise<T> {
    if (!this.interceptorsConfigured) this.init();

    return BaseAxios.get<T>(url);
  }

  public static async post<T = advAPIResponse, K extends object = object>(
    url: string,
    data: K,
  ): Promise<T> {
    if (!this.interceptorsConfigured) this.init();

    return BaseAxios.post<T>(url, data);
  }
}
