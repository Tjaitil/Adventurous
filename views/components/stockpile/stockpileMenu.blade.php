<div id="stck_menu"
    class="visible absolute z-10 w-36 max-w-[144px] overflow-scroll rounded-md border-2 border-stone-600 bg-stone-700 py-2 text-center text-xs text-white drop-shadow-xl">
    <p id="stck-current-item" class="mb-2 mt-0 font-bold"></p>
    <ul id="stck-menu-option-list">
        <x-stockpile.stockpileMenuItem :action-amount="'1'" />
        <x-stockpile.stockpileMenuItem :action-amount="'5'" />
        <x-stockpile.stockpileMenuItem :action-amount="'x'">
            <input
                class="custom-input w-full border-0 bg-transparent p-0 text-center text-xs"
                type="number" id="stck_menu_custom_amount" placeholder="">
        </x-stockpile.stockpileMenuItem>
        <x-stockpile.stockpileMenuItem id="stck_menu_all" :action-amount="'all'" />
    </ul>
</div>
