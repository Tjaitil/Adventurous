@props(['inventory'])
<x-borderInterfaceContainer id="inventory" class="w-[29%] float-right h-[600px] mt-0 z-20">
    <p class="text-white">Inventory <span id="inventory-status">
            {{ '(' . count($inventory) . ' / ' . '18' . ')' }}</span>
    </p>
    <x-inventory.inventoryItems :$inventory />
</x-borderInterfaceContainer>
