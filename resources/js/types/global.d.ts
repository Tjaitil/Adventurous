import type { PageProps as InertiaPageProps, Page } from '@inertiajs/core';
import type { Player } from './Player';

export interface AuthSharedProps extends InertiaPageProps {
  /**
   * SharedProps defined in middleware
   */
  player: Player;
}

export interface UnauthSharedProps extends InertiaPageProps {
  /**
   * SharedProps defined in middleware
   */
  player: null;
}

declare module '@inertiajs/vue3' {
  export function usePage<T = AuthSharedProps>(): Page<T>;
}
