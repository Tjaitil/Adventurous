
export class CustomFetchApi {
    private static route = "/api";

    private static fetchInstance<T extends {}>(method: 'PUT' | 'GET' | 'POST', url: string, data?: Object): Promise<T> {

        const requestInfo: RequestInit = {
            method: method,
            headers: { "Content-type": "application/json" },
        }
        if (data !== undefined) requestInfo.body = JSON.stringify(data);

        return fetch(this.route + url, requestInfo)
            .then((res) => res.json())
            .then((data: T) => {
                return data;
            })
            .catch((error) => {
                throw new Error(error);
            });
    }

    public static get<T extends {}>(url: string): Promise<T> {
        return this.fetchInstance<T>('GET', url);
    }


    public static post<T extends {}>(url: string, data: Object): Promise<T> {
        return fetch(this.route + url, {
            method: "POST",
            headers: { "Content-type": "application/json" },
            body: JSON.stringify(data),
        }).then((res) => res.json()).then((data: T) => {
            return data;
        })
            .catch((error) => {
                throw new Error(error);
            })
    }
}

(<any>window).AdvApi = CustomFetchApi;
