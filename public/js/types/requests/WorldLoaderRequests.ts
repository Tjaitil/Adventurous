export interface SetWorldRequest {
    new_map: {
        newX: number;
        newY: number;
    }
    is_new_map_string: boolean,
    new_destination: string;
}