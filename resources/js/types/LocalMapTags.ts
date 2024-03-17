import { BaseMapTag } from './BaseMapTag';

export interface LocalMapTags extends BaseMapTag {
    tagIdentifier: string;
    visible: boolean;
    mapParent: string;
}
