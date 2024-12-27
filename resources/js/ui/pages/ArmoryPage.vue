<template>
    <div class="flex flex-col gap-4">
        <h1 class="page_title">Armory</h1>
        <p>
            {{
                $t(
                    'Select a soldier to add equipment or click on worn equipment to remove',
                )
            }}
        </p>
        <div class="flex flex-row">
            <div class="sticky top-5 left-0 self-start w-1/5">
                <BaksCard variant="light">
                    <form
                        class="flex flex-col gap-y-4"
                        @submit.prevent="wearArmor()"
                    >
                        <h2 class="text-bold text-xl">
                            {{ $t('Add') }}
                        </h2>
                        <div>
                            <p>{{ $t('Selected soldier') }}</p>
                            <span v-if="selectedWarrior === null">{{
                                $t('None')
                            }}</span>
                            <span v-else>
                                {{ $t('Soldier #') }}{{ selectedWarrior }}</span
                            >
                            <span
                                v-if="hasSelectedWarriorError"
                                class="text-red-600 block"
                                >{{ $t('Please select a solider') }}</span
                            >
                            <input
                                v-model="selectedWarrior"
                                type="hidden"
                                name="warrior_id"
                            />
                        </div>
                        <div>
                            <span>{{ $t('Selected item') }}</span>
                            <BaseSelectedItem
                                v-model:amount="ammunitionAmount"
                                v-model:item="inventoryStore.selectedItem"
                            />
                        </div>
                        <div v-if="showWarriorHandOption">
                            <p>{{ $t('Select hand') }}</p>
                            <BaseRadio
                                id="warrior-hand-right"
                                v-model="warriorHand"
                                class="justify-start"
                                name="warrior-hand"
                                value="right_hand"
                            >
                                {{ $t('Right hand') }}
                            </BaseRadio>
                            <BaseRadio
                                id="warrior-hand-left"
                                v-model="warriorHand"
                                class="justify-start"
                                name="warrior-hand"
                                value="left_hand"
                            >
                                {{ $t('Left hand') }}
                            </BaseRadio>
                        </div>
                        <div v-if="showAmountOption" id="ranged_alt">
                            <label for="ammunition-amount" class="block">{{
                                $t('Select Amount')
                            }}</label>
                            <input
                                id="ammunition-amount"
                                name="ammunition-amount"
                                type="number"
                                min="1"
                                class="w-full"
                            />
                        </div>
                        <BaksButton
                            id="put_on_button"
                            :disabled="
                                inventoryStore.selectedItem === null ||
                                selectedWarrior === null
                            "
                            type="submit"
                            variant="secondary"
                            >{{ $t('Wear') }}</BaksButton
                        >
                        <BaseLoadingIcon
                            v-show="isLoading"
                            class="justify-self-center w-6 h-6"
                        />
                    </form>
                </BaksCard>
            </div>
            <div id="warrior_container" class="flex-grow">
                <WarriorArmoryWrapper
                    v-for="warrior in warriors"
                    :key="warrior.warrior_id"
                    :warrior="warrior"
                    :is-selected="selectedWarrior === warrior.warrior_id"
                    @toggle-select-warrior="handleToggleSelectWarrior"
                    @remove-armor="handleRemoveArmor"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ArmoryWarrior, ItemParts } from '@/types/Warrior';
import { AdvApi } from '@/AdvApi';
import { ref } from 'vue';
import { BaksButton, BaksCard } from 'baks-components-vue';
import BaseSelectedItem from '../components/base/BaseSelectedItem.vue';
import WarriorArmoryWrapper from '../components/armory/WarriorArmoryWrapper.vue';
import { useInventoryStore } from '../stores/InventoryStore';
import BaseLoadingIcon from '../components/base/BaseLoadingIcon.vue';
import BaseRadio from '../components/base/BaseRadio.vue';
import { CustomFetchApi } from '@/CustomFetchApi';

const inventoryStore = useInventoryStore();
const hasSelectedItemError = ref(false);

const isLoading = ref(false);

const warriors = ref<ArmoryWarrior[]>([]);

inventoryStore.$onAction(({ name, after }) => {
    if (name === 'setSelectedItem') {
        after(() => {
            toggleItemOptions();
        });
    }
});

const warriorHand = ref<string>('right_hand');

const ammunitionAmount = ref<number>(1);

const selectedWarrior = ref<number | null>(null);
const hasSelectedWarriorError = ref(false);

const handleToggleSelectWarrior = (id: number) => {
    selectedWarrior.value = id;
};

const fetchWarriors = async () => {
    try {
        isLoading.value = true;
        warriors.value = await AdvApi.get<ArmoryWarrior[]>('/armory/soldiers');
        isLoading.value = false;
    } catch (error) {
        return;
    }
};
fetchWarriors();

const showWarriorHandOption = ref(false);
const toggleItemOptions = () => {
    if (inventoryStore.selectedItem === null) {
        showWarriorHandOption.value = false;
        return;
    }

    if (
        inventoryStore.selectedItem.includes('sword') ||
        inventoryStore.selectedItem.includes('dagger')
    ) {
        showWarriorHandOption.value = true;
        showAmountOption.value = false;
    } else if (
        inventoryStore.selectedItem.includes('arrow') ||
        inventoryStore.selectedItem.includes('knives')
    ) {
        showWarriorHandOption.value = false;
        showAmountOption.value = true;
    } else {
        showWarriorHandOption.value = false;
        showAmountOption.value = false;
    }
};

const showAmountOption = ref(false);

const wearArmor = async () => {
    hasSelectedItemError.value = false;
    if (inventoryStore.selectedItem === null) {
        hasSelectedItemError.value = true;
        return;
    }

    if (selectedWarrior.value === null) {
        hasSelectedWarriorError.value = true;
        return;
    } else {
        hasSelectedWarriorError.value = false;
    }
    let hand;
    if (showWarriorHandOption.value) {
        hand = warriorHand.value;
    } else {
        hand = null;
    }

    try {
        isLoading.value = true;

        const response = await CustomFetchApi.post<
            ArmoryWarrior,
            WearArmorRequest
        >('/armory/soldier/add', {
            item: inventoryStore.selectedItem,
            warrior_id: selectedWarrior.value,
            hand,
            amount: ammunitionAmount.value,
        });

        updateWarriorArmory(response.data);

        ammunitionAmount.value = 1;

        inventoryStore.resetSelectedItem();
        inventoryStore.setShouldUpdateInventory(true);
        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
        return;
    }
};

const handleRemoveArmor = async (
    part: ItemParts,
    warrior_id: ArmoryWarrior['warrior_id'],
) => {
    hasSelectedWarriorError.value = false;

    try {
        isLoading.value = true;
        const response = await CustomFetchApi.post<
            ArmoryWarrior,
            RemoveArmorRequest
        >('/armory/soldier/remove', {
            warrior_id,
            is_removing: true,
            part,
        });
        updateWarriorArmory(response.data);

        inventoryStore.resetSelectedItem();
        inventoryStore.setShouldUpdateInventory(true);
        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
        return;
    }
};

const updateWarriorArmory = (warrior: ArmoryWarrior) => {
    const index = warriors.value.findIndex(
        w => w.warrior_id === warrior.warrior_id,
    );

    if (index === -1) {
        return;
    }

    warriors.value[index] = warrior;
};

interface WearArmorRequest {
    item: string;
    warrior_id: number;
    hand: string | null;
    amount: number;
}

interface RemoveArmorRequest {
    warrior_id: number;
    is_removing: boolean;
    part: ItemParts;
}
</script>

<style>
#warrior_container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 200px));
    grid-gap: 16px;
    justify-content: center;
}
</style>
