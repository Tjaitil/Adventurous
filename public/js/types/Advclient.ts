import { DaqloonFightingArea } from "./../gamepieces/DaqloonFightingArea";
import { Building } from "./../gamepieces/Building";
import { HUD } from "./../clientScripts/HUD.js";
import { StaticGameObject } from "./gamepieces/StaticGameObject";

export interface GameProperties {
    HUD: typeof HUD;
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
    assetsPath: string;
}

export interface loadWorldParamters {
    newxBase?: number;
    newyBase?: number;
    newMap?: {
        newX: number;
        newY: number;
    };
    newDestination?: string;
    method?: "changeMap";
    startPointType?: boolean;
}

export interface WorldMapData {
    buildings: Building[];
    daqloon_fighting_areas: DaqloonFightingArea[];
    objects: StaticGameObject[];
}
