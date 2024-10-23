import { WorldMapData } from '../Advclient';
import { advAPIResponse } from './AdvResponse';

export type GetWorldResponse = advAPIResponse<{
    current_map: string;
    changed_location: string;
    map_data: WorldMapData;
    events: [];
}>;
