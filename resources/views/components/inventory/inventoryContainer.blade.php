@props(['inventory'])
@php
    $inventoryCapacity = 18;
    $isInventoryFull = count($inventory) === $inventoryCapacity;
@endphp
<baks-card variant="dark" id="inventory" class="z-20 relative w-[29%] float-right h-[600px] mt-0">
    <p class="mb-4">Inventory <span id="inventory-status" @class(['not-able-color' => $isInventoryFull])>
            {{ sprintf('(%d / %d)', count($inventory), $inventoryCapacity) }}</span>
    </p>
    <x-inventory.inventoryItems :$inventory />
</baks-card>
