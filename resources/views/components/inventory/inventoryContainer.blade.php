@props(['inventory'])
@php
    $inventoryCapacity = 18;
    $isInventoryFull = count($inventory) === $inventoryCapacity;
@endphp
<x-borderInterfaceContainer id="inventory" class="w-[29%] float-right h-[600px] mt-0 z-20">
    <p class="text-white mb-4">Inventory <span id="inventory-status" @class(['not-able-color' => $isInventoryFull])>
            {{ sprintf('(%d / %d)', count($inventory), $inventoryCapacity) }}</span>
    </p>
    <x-inventory.inventoryItems :$inventory />
</x-borderInterfaceContainer>
