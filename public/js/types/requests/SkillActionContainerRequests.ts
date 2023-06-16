import { CropResource } from './../CropResource';
import { advAPIResponse } from './../responses/AdvResponse';
export interface GetSkillActionDataRequest extends advAPIResponse {
    data: {
        workforce_data: {
            avail_workforce: number;
        };
        crops: CropResource[];
        minerals: [];
    }
}