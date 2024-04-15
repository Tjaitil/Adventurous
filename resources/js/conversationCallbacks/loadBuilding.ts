import { inputHandler } from '@/clientScripts/inputHandler';

export const loadBuildingCallback = (building: string) => {
    inputHandler.fetchBuilding(building);
};
