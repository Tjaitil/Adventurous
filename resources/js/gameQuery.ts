import type { GameQueryMap } from '@/types/GameEvents';

type GameQueryName = keyof GameQueryMap;

type GameQueryHandler<K extends GameQueryName> = (
  params: GameQueryMap[K]['params'],
) => GameQueryMap[K]['result'] | Promise<GameQueryMap[K]['result']>;

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
    this.providers[name] = handler as never;

    return () => {
      if (this.providers[name] === handler) {
        this.providers[name] = undefined;
      }
    };
  }

  /**
   * Rejects if no provider is registered for the query.
   */
  async query<K extends GameQueryName>(
    name: K,
    ...[params]: GameQueryMap[K]['params'] extends undefined
      ? []
      : [params: GameQueryMap[K]['params']]
  ): Promise<GameQueryMap[K]['result']> {
    const handler = this.providers[name];
    if (handler === undefined) {
      throw new Error(`No provider registered for query: ${name}`);
    }
    return handler(params);
  }
}

export const gameQuery = new GameQuery();
