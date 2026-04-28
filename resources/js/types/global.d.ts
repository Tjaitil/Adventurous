import type { Player } from './Player';

declare module '@inertiajs/core' {
  interface InertiaConfig {
    sharedPageProps: {
      player: Player | null;
      userId: number | null;
    };
  }
}
