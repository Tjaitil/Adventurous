import type { GameQueryMap } from '@/types/GameEvents';

type GameQueryName = Extract<keyof GameQueryMap, string>;

type GameQueryParams<K extends GameQueryName> = GameQueryMap[K] extends {
  params: infer P;
}
  ? P
  : undefined;

type GameQueryHandler<K extends GameQueryName> = (
  params: GameQueryParams<K>,
) => GameQueryMap[K]['result'];

/**
 * Module for UI to query game state and register handlers for game-side queries.
 * NOTE: Most likely you can use `gameEventBus` for most game-related events instead of directly querying the game state.
 */
class GameQuery {
  private providers: {
    [K in GameQueryName]?: GameQueryHandler<K>;
  } = {};

  /**
   * Register the game-side handler for a query. Returns an unregister function.
   *
   * @throws Error if a provider is already registered for the query
   */
  provide<K extends GameQueryName>(
    name: K,
    handler: GameQueryHandler<K>,
  ): () => void {
    if (this.providers[name] !== undefined) {
      throw new Error(`Query provider already registered: ${name}`);
    }
    this.providers[name] = handler;

    return () => {
      if (this.providers[name] === handler) {
        this.providers[name] = undefined;
      }
    };
  }

  /**
   * Throws if no provider is registered for the query.
   */
  query<K extends GameQueryName>(
    name: K,
    ...[params]: [GameQueryParams<K>] extends [undefined]
      ? []
      : [params: GameQueryParams<K>]
  ): GameQueryMap[K]['result'] {
    const handler = this.providers[name];
    if (handler === undefined) {
      throw new Error(`No provider registered for query: ${name}`);
    }
    return handler(params as GameQueryParams<K>);
  }
}

export const gameQuery = new GameQuery();
