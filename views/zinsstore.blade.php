<h1 class="page_title">Zins Store</h1>
<div id="zinsstore">
    <x-helpSection>
        Zins is a trader who trades daqloon loot. He
        <br>
        Zins is willing to trade daqloon horns and daqloon scales.
    </x-helpSection>
    <x-store.storeContainer :store-resource="$store_resource" :options="[
        'item_requirements' => false,
        'item_information' => false,
        'show-input-amount' => true,
        'show-requirements' => true,
    ]" />
</div>
