<template>
  <UApp>
    <div class="fixed right-4 bottom-4 z-50">
      <div
        v-if="open"
        class="mb-2 w-80 overflow-auto rounded-lg border border-orange-500 bg-gray-900 p-2 shadow-xl"
        style="max-height: 480px"
      >
        <p
          class="mb-2 text-xs font-bold tracking-wide text-orange-400 uppercase"
        >
          Dev Tools
        </p>
        <UTabs :items="subTabs" orientation="horizontal">
          <template #give-item>
            <div class="mt-2 space-y-2">
              <USelect
                v-model="selectedItem"
                :items="itemOptions"
                placeholder="Select item..."
              />
              <UInput
                v-model.number="giveAmount"
                type="number"
                :min="1"
                placeholder="Amount"
                size="sm"
              />
              <UButton size="sm" :loading="givingItem" @click="giveItem"
                >Give Item</UButton
              >
            </div>
          </template>
          <template #teleport-location>
            <div class="mt-2 flex flex-col items-start space-y-2">
              <USelect
                v-model="selectedLocation"
                :items="locationOptions"
                placeholder="Select location..."
                size="sm"
              />
              <UButton
                size="sm"
                :loading="teleporting"
                @click="teleportToLocation"
                >Teleport</UButton
              >
            </div>
          </template>
        </UTabs>
      </div>
      <button
        class="float-right rounded-md border border-orange-500 bg-gray-900 px-3 py-1 text-xs font-bold text-orange-400 shadow-lg hover:bg-gray-800"
        @click="open = !open"
      >
        {{ open ? 'Close Dev' : 'Dev Tools' }}
      </button>
    </div>
  </UApp>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { TabsItem } from '@nuxt/ui';
import { CustomFetchApi } from '@/CustomFetchApi';

const open = ref(false);

const subTabs: TabsItem[] = [
  { label: 'Give Item', slot: 'give-item' as const },
  { label: 'Teleport Location', slot: 'teleport-location' as const },
];

const itemOptions = ref<{ label: string; value: string }[]>([]);
const selectedItem = ref<string | undefined>(undefined);
const giveAmount = ref(1);
const givingItem = ref(false);
const giveMessage = ref('');
const giveError = ref(false);

onMounted(async () => {
  try {
    const response = await CustomFetchApi.get<{
      data: { name: string; item_id: number }[];
    }>('/dev/admin/items');
    itemOptions.value = response.data.data.map(item => ({
      label: item.name,
      value: item.name,
    }));
  } catch {
    // non-critical
  }

  try {
    const locResponse = await CustomFetchApi.get<{
      data: { label: string; value: string }[];
    }>('/dev/admin/locations');
    locationOptions.value = locResponse.data.data;
  } catch {
    // non-critical
  }
});

async function giveItem() {
  if (!selectedItem.value) return;
  givingItem.value = true;
  giveMessage.value = '';
  try {
    await CustomFetchApi.post('/dev/admin/item/give', {
      item: selectedItem.value,
      amount: giveAmount.value,
    });
    giveMessage.value = `Gave ${giveAmount.value}x ${selectedItem.value}`;
    giveError.value = false;
  } catch {
    giveMessage.value = 'Failed to give item';
    giveError.value = true;
  } finally {
    givingItem.value = false;
  }
}

// --- Teleport Location ---
const locationOptions = ref<{ label: string; value: string }[]>([]);
const selectedLocation = ref<string | undefined>(undefined);
const teleporting = ref(false);
const teleportMessage = ref('');
const teleportError = ref(false);

async function teleportToLocation() {
  if (!selectedLocation.value) return;
  teleporting.value = true;
  teleportMessage.value = '';
  try {
    await CustomFetchApi.post('/dev/admin/teleport/location', {
      map: selectedLocation.value,
    });
    teleportMessage.value = `Teleporting to ${selectedLocation.value}…`;
    teleportError.value = false;
    window.location.reload();
  } catch {
    teleportMessage.value = 'Teleport failed';
    teleportError.value = true;
  } finally {
    teleporting.value = false;
  }
}
</script>
