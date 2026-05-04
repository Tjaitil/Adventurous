<template>
  <UApp>
    <div class="fixed right-4 bottom-4 z-50">
      <div
        v-if="open"
        class="mb-2 w-80 rounded-lg border border-orange-500 bg-gray-900 p-3 shadow-xl"
      >
        <p
          class="mb-3 text-xs font-bold tracking-wide text-orange-400 uppercase"
        >
          Dev Tools
          <span
            v-if="frozen"
            class="ml-2 rounded bg-red-800 px-1 py-0.5 text-red-200"
            >FROZEN</span
          >
        </p>

        <USelect
          v-model="action"
          :items="actions"
          placeholder="Select action…"
          class="mb-3"
        />

        <!-- Give Item -->
        <div v-if="action === 'give-item'" class="space-y-2">
          <USelect
            v-model="selectedItem"
            :items="itemOptions"
            placeholder="Select item…"
          />
          <UInput
            v-model.number="giveAmount"
            type="number"
            :min="1"
            placeholder="Amount"
            size="sm"
          />
          <div class="flex items-center gap-2">
            <UButton size="sm" :loading="givingItem" @click="giveItem"
              >Give</UButton
            >
            <p
              v-if="giveMessage"
              class="text-xs"
              :class="giveError ? 'text-red-400' : 'text-green-400'"
            >
              {{ giveMessage }}
            </p>
          </div>
        </div>

        <!-- Teleport Location -->
        <div v-else-if="action === 'teleport-location'" class="space-y-2">
          <USelect
            v-model="selectedLocation"
            :items="locationOptions"
            placeholder="Select location…"
          />
          <div class="flex items-center gap-2">
            <UButton
              size="sm"
              :loading="teleporting"
              @click="teleportToLocation"
              >Teleport</UButton
            >
            <p
              v-if="teleportMessage"
              class="text-xs"
              :class="teleportError ? 'text-red-400' : 'text-green-400'"
            >
              {{ teleportMessage }}
            </p>
          </div>
        </div>

        <div v-else-if="action === 'set-userdata'" class="space-y-2">
          <label class="block text-xs text-gray-400">
            Hunger (0–100)
            <UInput
              v-model.number="userDataForm.hunger"
              type="number"
              :min="0"
              :max="100"
              size="sm"
              class="mt-1"
            />
          </label>
          <label class="block text-xs text-gray-400">
            Stockpile max
            <UInput
              v-model.number="userDataForm.stockpile_max_amount"
              type="number"
              :min="1"
              size="sm"
              class="mt-1"
            />
          </label>
          <div class="flex gap-3 pt-1">
            <label
              class="flex cursor-pointer items-center gap-1 text-xs text-gray-400"
            >
              <input
                v-model="userDataForm.frajrite_items"
                type="checkbox"
                class="accent-orange-400"
              />
              frajrite items
            </label>
            <label
              class="flex cursor-pointer items-center gap-1 text-xs text-gray-400"
            >
              <input
                v-model="userDataForm.wujkin_items"
                type="checkbox"
                class="accent-orange-400"
              />
              wujkin items
            </label>
          </div>
          <div class="flex items-center gap-2 pt-1">
            <UButton size="sm" :loading="savingUserData" @click="saveUserData"
              >Save</UButton
            >
            <p
              v-if="userDataMessage"
              class="text-xs"
              :class="userDataError ? 'text-red-400' : 'text-green-400'"
            >
              {{ userDataMessage }}
            </p>
          </div>
        </div>

        <!-- Freeze DB -->
        <div v-else-if="action === 'freeze'" class="space-y-2">
          <p class="text-xs text-gray-400">
            When frozen all DB writes outside <code>/dev/admin/*</code> are
            wrapped in a rolled-back transaction — nothing persists.
          </p>
          <UButton
            size="sm"
            :color="frozen ? 'error' : 'primary'"
            :loading="togglingFreeze"
            @click="toggleFreeze"
          >
            {{ frozen ? 'Unfreeze DB' : 'Freeze DB' }}
          </UButton>
        </div>
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
import type { SelectItem } from '@nuxt/ui';
import { CustomFetchApi } from '@/CustomFetchApi';

const open = ref(false);

const actions: SelectItem[] = [
  { label: 'Give Item', value: 'give-item' },
  { label: 'Teleport Location', value: 'teleport-location' },
  { label: 'Set UserData', value: 'set-userdata' },
  { label: 'Freeze DB', value: 'freeze' },
];
const action = ref<string | undefined>(undefined);

const itemOptions = ref<SelectItem[]>([]);
const selectedItem = ref<string | undefined>(undefined);
const giveAmount = ref(1);
const givingItem = ref(false);
const giveMessage = ref('');
const giveError = ref(false);

async function giveItem() {
  if (!selectedItem.value) return;
  givingItem.value = true;
  giveMessage.value = '';
  try {
    await CustomFetchApi.post('/dev/admin/item/give', {
      item: selectedItem.value,
      amount: giveAmount.value,
    });
    giveMessage.value = `Gave ${giveAmount.value}×`;
    giveError.value = false;
  } catch {
    giveMessage.value = 'Failed';
    giveError.value = true;
  } finally {
    givingItem.value = false;
  }
}

const locationOptions = ref<SelectItem[]>([]);
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
    teleportMessage.value = `Teleporting…`;
    teleportError.value = false;
    window.location.reload();
  } catch {
    teleportMessage.value = 'Failed';
    teleportError.value = true;
  } finally {
    teleporting.value = false;
  }
}

const userDataForm = ref({
  hunger: 100,
  stockpile_max_amount: 100,
  frajrite_items: false,
  wujkin_items: false,
});
const savingUserData = ref(false);
const userDataMessage = ref('');
const userDataError = ref(false);

async function saveUserData() {
  savingUserData.value = true;
  userDataMessage.value = '';
  try {
    await CustomFetchApi.post('/dev/admin/userdata', userDataForm.value);
    userDataMessage.value = 'Saved';
    userDataError.value = false;
  } catch {
    userDataMessage.value = 'Failed';
    userDataError.value = true;
  } finally {
    savingUserData.value = false;
  }
}

const frozen = ref(false);
const togglingFreeze = ref(false);

async function toggleFreeze() {
  togglingFreeze.value = true;
  try {
    const res = await CustomFetchApi.post<{ frozen: boolean }>(
      '/dev/admin/freeze',
      {},
    );
    frozen.value = res.data.frozen;
  } finally {
    togglingFreeze.value = false;
  }
}

onMounted(async () => {
  const [itemsRes, locationsRes, stateRes] = await Promise.allSettled([
    CustomFetchApi.get<{ data: { name: string }[] }>('/dev/admin/items'),
    CustomFetchApi.get<{ data: { label: string; value: string }[] }>(
      '/dev/admin/locations',
    ),
    CustomFetchApi.get<{ frozen: boolean }>('/dev/admin/freeze'),
  ]);

  setTimeout(() => {
    CustomFetchApi.get<{ frozen: boolean }>('/dev/admin/freeze')
      .then(res => {
        frozen.value = res.data.frozen;
      })
      .catch(() => false);
  }, 5000);

  if (itemsRes.status === 'fulfilled') {
    itemOptions.value = itemsRes.value.data.data.map(i => ({
      label: i.name,
      value: i.name,
    }));
  }
  if (locationsRes.status === 'fulfilled') {
    locationOptions.value = locationsRes.value.data.data;
  }
  if (stateRes.status === 'fulfilled') {
    frozen.value = stateRes.value.data.frozen;
  }
});
</script>
