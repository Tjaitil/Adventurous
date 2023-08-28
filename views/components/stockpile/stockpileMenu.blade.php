<div id="stck_menu"
    class="visible absolute z-10 w-36 max-w-[144px] overflow-scroll rounded-md border-2 border-stone-600 bg-stone-700 py-2 text-center text-xs text-white drop-shadow-xl">
    <p id="stck-current-item" class="mb-2 mt-0 font-bold"></p>
    <ul id="stck-menu-option-list">
        @component('components.stockpile.stockpileMenuItem', [
            'action_amount' => '1',
        ])
            Insert 1
        @endcomponent
        @component('components.stockpile.stockpileMenuItem', [
            'action_amount' => '5',
        ])
            Insert 5
        @endcomponent
        @component('components.stockpile.stockpileMenuItem', [
            'action_amount' => 'x',
        ])
            <input
                class="custom-input w-full border-0 bg-transparent p-0 text-center text-xs"
                type="number" id="stck_menu_custom_amount" placeholder="">
        @endcomponent
        @component('components.stockpile.stockpileMenuItem', [
            'id' => 'stck_menu_all',
            'action_amount' => 'all',
        ])
            Insert all
        @endcomponent
    </ul>
</div>
