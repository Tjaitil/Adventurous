export interface BaseMapTag {
    id: number;
    x: number;
    y: number;
    text?: string;
    src?: string;
    type: "city" | "placeName" | "icon";
    mapParent: string;
    tagType: string;
}
