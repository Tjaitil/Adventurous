/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
import { useInventoryStore } from './ui/stores/InventoryStore';
import { InventoryItem } from '@/types/InventoryItem';
import { useSkillsStore } from './ui/stores/SkillsStore';

window.Pusher = Pusher;

const echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
  wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
  enabledTransports: ['ws', 'wss'],
});
echo
  .private(`game-state.${window.user_id.toString()}`)
  .listen('InventoryUpdated', (e: { Inventory: InventoryItem[] }) => {
    useInventoryStore().setInventoryItems(e.Inventory);
  })
  .listen('SkillsUpdated', () => {
    useSkillsStore().setHandleXpGainedEvent(true);
  });
