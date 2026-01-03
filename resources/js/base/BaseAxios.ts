import { checkResponse } from '../ajax';
import { advAPIResponse } from '../types/Responses/AdvResponse';
import { Axios } from 'axios';

type responseType<T> = T extends advAPIResponse ? T : T;
export class BaseAxios {
  private static route = window.location.origin;

  protected static Axios: Axios;

  protected static createAxiosInstance() {
    this.Axios = new Axios({
      baseURL: this.route,
      timeout: 1000,
      headers: { 'Content-type': 'application/json' },
    });

    this.Axios.interceptors.response.use(response => response.data);
  }

  protected static fetchInstance<T extends {}>(
    method: 'PUT' | 'GET' | 'POST',
    url: string,
    data?: object,
  ): Promise<T> {
    const requestInfo: RequestInit = {
      method: method,
      headers: { 'Content-type': 'application/json' },
    };
    if (data !== undefined) requestInfo.body = JSON.stringify(data);

    return fetch(this.route + url, requestInfo)
      .then(res => {
        if (!res.ok) {
          return res.json().then(data => {
            return Promise.reject(data);
          });
        }
        return res.json();
      })
      .then((data: T) => {
        checkResponse(data);
        return data;
      })
      .catch(errorMessage => {
        checkResponse(errorMessage);
        return Promise.reject(errorMessage);
      });
  }

  public static async baseGet<T = advAPIResponse>(url: string): Promise<T> {
    if (!this.Axios) this.createAxiosInstance();

    return await this.Axios.post<T>('GET', url).then(
      async response => response.data,
    );
  }

  public static async basePost<T = advAPIResponse>(
    url: string,
    data: object,
  ): Promise<T> {
    if (!this.Axios) this.createAxiosInstance();

    return await this.Axios.post<T>('POST', url, data).then(
      async response => response.data,
    );
  }

  public static foo<T, R>() {
    return this.Axios.request<T, R>({
      url: '/api/armory/add',
      method: 'POST',
      data: {
        foo: 'bar',
      },
    }).then(response => {
      return response;
    });
  }
}
