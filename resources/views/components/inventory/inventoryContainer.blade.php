@props(['inventory'])
@php
    $inventoryCapacity = 18;
    $isInventoryFull = count($inventory) === $inventoryCapacity;
@endphp
<div class="w-[29%] float-right h-[600px] mt-0 z-20">
    <baks-card variant="dark" id="inventory">
        <p class="mb-4">Inventory <span id="inventory-status" @class(['not-able-color' => $isInventoryFull])>
                {{ sprintf('(%d / %d)', count($inventory), $inventoryCapacity) }}</span>
        </p>
        <x-inventory.inventoryItems :$inventory />
    </baks-card>
</div>
