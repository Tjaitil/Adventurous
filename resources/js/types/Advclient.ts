import { DaqloonFightingArea } from './../gamepieces/DaqloonFightingArea';
import { Building } from './../gamepieces/Building';
import { HUD } from './../clientScripts/HUD';
import { StaticGameObject } from './gamepieces/StaticGameObject';

export interface GameProperties {
    HUD?: typeof HUD;
    duration: 0;
    requestId: number;
    pauseID: null;
    timestamp: number;
    xbase: number;
    ybase: number;
    currentMap: string;
    gameState: string;
    device: string;
    building: string;
    inBuilding: boolean;
    checkingPerson: string;
    delta: number;
}

export type loadWorldParameters =
    | RespawnLoadWorld
    | TravelLoadWorld
    | MovemenetNavigationWorldParameters;

export type RespawnLoadWorld = {
    method: 'respawn';
};

export type TravelLoadWorld = {
    method: 'travel';
    newDestination: string;
    hasStartPointType: boolean;
};

export type MovemenetNavigationWorldParameters = {
    method: 'nextMap';
    newMap?: {
        newX: number;
        newY: number;
    };
    newxBase: number;
    newyBase: number;
};

export interface WorldMapData {
    buildings: Building[];
    daqloon_fighting_areas: DaqloonFightingArea[];
    objects: StaticGameObject[];
}
