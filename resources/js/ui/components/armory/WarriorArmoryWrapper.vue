<template>
    <div
        class="armory_view cursor-pointer flex flex-col gap-y-2 bg-primary-800 p-2 pixelated-corners-sm border-gray-950/60 text-white border-4 relative"
        :class="[isSelected ? 'border-8' : '']"
        @click="toggleSelectWarrior"
    >
        <div class="flex gap-2 justify-center items-center">
            <img
                class="type_icon max-h-12 max-w-12 basis-2/4"
                :src="AssetPaths.getImagePngPath(warrior.type + ' icon')"
                alt="warrior-icon"
            />
            <div>
                <span class="text-left">
                    {{ $t('Soldier') }} #{{ warrior.warrior_id }}</span
                >
            </div>
        </div>
        <div class="armory_view_part_grid">
            <WarriorArmoryPart
                class="helm-grid-area"
                :part="warrior.armory.helm"
                @click="handleRemoveItem('helm')"
            />
            <div class="ammunition-grid-area relative">
                <span class="absolute bottom-0 right-1 text-white">{{
                    warrior.armory.ammunition_amount > 0
                        ? warrior.armory.ammunition_amount
                        : ''
                }}</span>
                <WarriorArmoryPart
                    :part="warrior.armory.ammunition"
                    @click="handleRemoveItem('ammunition')"
                />
            </div>
            <WarriorArmoryPart
                class="right-hand-grid-area"
                :part="warrior.armory.right_hand"
                @click="handleRemoveItem('right_hand')"
            />
            <WarriorArmoryPart
                class="body-grid-area"
                :part="warrior.armory.body"
                @click="handleRemoveItem('body')"
            />
            <WarriorArmoryPart
                class="left-hand-grid-area"
                :part="warrior.armory.left_hand"
                @click="handleRemoveItem('left_hand')"
            />
            <WarriorArmoryPart
                class="legs-grid-area"
                :part="warrior.armory.legs"
                @click="handleRemoveItem('legs')"
            />
            <WarriorArmoryPart
                class="boots-grid-area"
                :part="warrior.armory.boots"
                @click="handleRemoveItem('boots')"
            />
        </div>
        <div class="grid grid-cols-[60px_40px]">
            <span class="text-left">{{ $t('Attack') }}</span
            ><span>{{ warrior.armory.attack }}</span>
            <span class="text-left">{{ $t('Defence') }}</span
            ><span>{{ warrior.armory.defence }}</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ArmoryWarrior, ItemParts } from '@/types/Warrior';
import { AssetPaths } from '@/clientScripts/ImagePath';
import WarriorArmoryPart from './WarriorArmoryPart.vue';

interface Props {
    warrior: ArmoryWarrior;
    isSelected: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    removeArmor: [item: ItemParts, warrior_id: number];
    toggleSelectWarrior: [id: number];
}>();

const handleRemoveItem = (item: ItemParts) => {
    if (!props.warrior.armory[item]) return;
    emit('removeArmor', item, props.warrior.warrior_id);
};

const toggleSelectWarrior = () => {
    emit('toggleSelectWarrior', props.warrior.warrior_id);
};
</script>
<style scoped>
.ammunition-grid-area {
    grid-area: ammunition;
}
.helm-grid-area {
    grid-area: helm;
}
.left-hand-grid-area {
    grid-area: left_hand;
}
.body-grid-area {
    grid-area: body;
}
.right-hand-grid-area {
    grid-area: right_hand;
}
.legs-grid-area {
    grid-area: legs;
}
.boots-grid-area {
    grid-area: boots;
}
.armory_view_part_grid {
    display: grid;
    grid-template-columns: repeat(3, 48px);
    grid-auto-rows: 48px;
    grid-template-areas: '. helm ammunition' 'right_hand body left_hand' '. legs .' '. boots . ';
    justify-content: center;
    gap: 10px;
}
@media all and (max-width: 830px) {
    #armory_view_span {
        top: -75%;
        left: 26%;
    }
}
</style>
