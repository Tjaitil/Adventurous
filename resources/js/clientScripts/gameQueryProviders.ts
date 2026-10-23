import { Game } from '@/advclient';
import { GamePieces } from '@/clientScripts/gamePieces';
import { gameQuery } from '@/gameQuery';

let unregisters: Array<() => void> = [];

export const unregisterGameQueryProviders = () => {
  unregisters.forEach(unregister => {
    unregister();
  });
  unregisters = [];
};

export const registerGameQueryProviders = () => {
  unregisterGameQueryProviders();

  unregisters.push(
    gameQuery.provide('PLAYER_POSITION', () => ({
      x: GamePieces.player.xpos,
      y: GamePieces.player.ypos,
      currentMap: Game.properties.currentMap,
    })),
  );
};
