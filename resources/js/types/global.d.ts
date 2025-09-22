import { PageProps as InertiaPageProps, Page } from '@inertiajs/core';

export interface SharedProps extends InertiaPageProps {
  /**
   * SharedProps defined in middleware
   */
  // player: Player;
}

declare module '@inertiajs/vue3' {
  export function usePage(): Page<SharedProps>;
}
