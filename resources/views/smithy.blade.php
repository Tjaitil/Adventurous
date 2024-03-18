<h1 class="page_title">{{ $title }}</h1>
<div id="smith">
    <x-helpSection>
        <x-profiencyBenefitNotice :is-active="$isDiscountActive" :notice-text="'Miner discount of ' . $store_resource->store_value_modifier_as_percentage . ' % is active'" />
    </x-helpSection>
    <x-store.storeContainer :store-resource="$store_resource" :options="[
        'item-requirements' => true,
        'item-information' => true,
    ]" />
</div>
