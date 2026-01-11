import type { GameLocation } from '@/types/GameLocations';

export const getCropLocations = (): GameLocation[] => {
  return ['krasnur', 'towhar'];
};

export const getMineLocations = (): GameLocation[] => {
  return ['golbak', 'snerpiir'];
};

// TODO: stricten type when location list is complete
export const isCropLocation = (location: string): location is GameLocation => {
  return getCropLocations().includes(location as GameLocation);
};

// TODO: stricten type when location list is complete
export const isMineLocation = (location: string): location is GameLocation => {
  return getMineLocations().includes(location as GameLocation);
};
