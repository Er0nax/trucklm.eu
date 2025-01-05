import axios, {AxiosInstance, AxiosResponse} from 'axios';
import SiteModule from "@/shared/SiteModule";
import {SwapperResponse} from "@/types";

class RequestHelper {
    private axiosInstance: AxiosInstance;

    constructor() {
        // Create an Axios instance to use in your helper
        this.axiosInstance = axios.create({
            baseURL: SiteModule.baseUrl + SiteModule.language + '/api/',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            responseType: 'json',
            validateStatus: (status: number) => {
                return true;
            }
        });
    }

    /**
     * Method to get HTML with POST variables
     * @param url
     * @param data
     */
    public async post(url: string, data: Record<string, any> | null): Promise<SwapperResponse> {
        try {
            // add custom params
            const payload = {
                ...(data || {}),
                token: SiteModule.token,
            };

            // fetch
            const response: AxiosResponse<string> = await this.axiosInstance.post(url, payload);

            // convert to custom response
            return response.data as unknown as SwapperResponse;

        } catch (error) {
            console.error('Error fetching data.');
            throw error;
        }
    }
}

export default new RequestHelper();