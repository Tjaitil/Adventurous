<template>
  <AppLayoutWithAside>
    <div
      id="client-container"
      class="relative flex flex-row gap-x-4 transition-opacity duration-500 ease-in"
    >
      <div class="relative grow">
        <LogModal />
        <ConversationContainer />
        <div id="game-screen-container">
          <GameScreen />
          <ScreenHUD :hunger="hunger" :health="{ current: 100, max: 100 }" />
        </div>
        <ItemTooltip />
        <GameMap :map-location="mapLocation" />
        <ClientOverlayWrapper />
        <input id="draw_checkbox" type="checkbox" name="" />
      </div>
      <CartInventoryPanel />
    </div>
    <template #aside>
      <SidebarSection
        :profiency-statuses
        :username
        :profiency
        :location
        :init-messages
        :init-levels
        :diplomacy-resource
      />
    </template>
  </AppLayoutWithAside>
</template>

<script setup lang="ts">
import AppLayoutWithAside from '../components/layout/AppLayoutWithAside.vue';
import ConversationContainer from '../components/ConversationContainer.vue';
import GameScreen from '../components/HUD/GameScreen.vue';
import ScreenHUD from '../components/HUD/ScreenHUD.vue';
import ItemTooltip from '../components/HUD/ItemTooltip.vue';
import GameMap from '../components/HUD/GameMap.vue';
import { Game } from '@/advclient';
import { onMounted } from 'vue';
import LogModal from '../components/HUD/LogModal.vue';
import SidebarSection from '../components/HUD/SidebarSection.vue';
import type { DiplomacyResource } from '@/types/Diplomacy';
import type { UserLevels } from '@/types/UserLevels';
import type { GameLog } from '@/types/GameLog';
import type { ProficiencyStatuses } from '@/types/ProficiencyStatuses';
import ClientOverlayWrapper from '../components/ClientOverlayWrapper.vue';
import { usePlayerStore } from '../stores/PlayerStore';
import CartInventoryPanel from '../components/CartAndInventoryPanel.vue';
import { useUserCartStore } from '../stores/UserCartStore';
import type {
  UserCartItemResource,
  UserCartItemResourceCollection,
} from '@/types/TraderAssignment';

interface Props {
  hunger: {
    current: number;
    max: number;
  };
  //TODO: Set up health from backend
  mapLocation: string;
  username: string;
  profiency: string;
  location: string;
  initMessages: GameLog[];
  initLevels: UserLevels;
  diplomacyResource: DiplomacyResource;
  profiencyStatuses: ProficiencyStatuses;
  cartItems: UserCartItemResourceCollection;
}
const { username, location, profiencyStatuses, cartItems } =
  defineProps<Props>();

const playerStore = usePlayerStore();
playerStore.location = location;
playerStore.username = username;

const cartStore = useUserCartStore();
cartStore.setCart(profiencyStatuses.trader.cart);
cartStore.setCartItems(cartItems.data);

onMounted(async () => {
  await Game.getWorld().then(() => {
    Game.setup();
  });
});
</script>
<style scoped>
section {
  background: radial-gradient(
    var(--layoutBgColor),
    var(--layoutBgColorShadowPerc),
    var(--layoutBgColorShadow)
  );
}
</style>
