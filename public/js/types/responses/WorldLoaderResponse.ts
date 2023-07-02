import { WorldMapData } from "../Advclient.js";

export interface GetWorldResponse {
    data: {
        current_map: string;
        changed_location: string;
        map_data: WorldMapData;
        events: [];
    }
}