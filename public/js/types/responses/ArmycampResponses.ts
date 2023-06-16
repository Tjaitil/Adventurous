import { WarriorResource } from './../WarriorResource';
import { AdvApi } from './../../AdvApi';

export interface RunWarriorActionResponse extends AdvApi {
    data: {
        warriors: WarriorResource[]
    }
}

export interface RunSingleWarriorActionResponse extends AdvApi {
    data: {
        warrior: WarriorResource
    }
}