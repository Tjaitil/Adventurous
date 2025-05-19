import type Pusher from 'pusher-js';

declare global {
    interface Window {
        user_id: number;
        Pusher: typeof Pusher;
    }
}
