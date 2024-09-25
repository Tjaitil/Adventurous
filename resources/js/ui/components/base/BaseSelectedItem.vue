<template>
    <div id="selected-item-data-wrapper">
        <div
            class="mt-1 mb-1 mx-auto border-primary-900 border-2 w-12 h-12 box-content border-outset"
            :role="isItemSet ? 'button' : ''"
            :title="isItemSet ? 'Remove item' : ''"
            :aria-label="isItemSet ? 'Remove item' : ''"
        >
            <img
                v-if="item !== null"
                :src="AssetPaths.getImagePngPath(item)"
                alt="item-selected"
                @click="removeItem()"
            />
        </div>
        <div id="selected_item_amount_wrapper">
            <template v-if="showAmountInput">
                <label for="selected-item-amount"><slot>amount</slot></label>
                <input
                    id="selected-item-amount"
                    v-model="amount"
                    type="number"
                    name="selected-item-amount"
                />
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { AssetPaths } from '@/clientScripts/ImagePath';
import { computed } from 'vue';

interface Props {
    item: string | null;
    showAmountInput?: boolean;
}

const amount = defineModel<number>('amount');

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:item': [null];
}>();

const isItemSet = computed(() => props.item !== null);

const removeItem = () => {
    amount.value = 1;
    emit('update:item', null);
};
</script>
