<h1 class="page_title">{{ $title }}</h1>
<div id="bakery">
    <x-helpSection>
        <p>Here you can make food to relieve your hunger status.
            <br> For more information head to <a href="gameguide/bakery" target="_blank">gameguide/bakery</a>
        </p>
        <x-profiencyBenefitNotice :is-active="$isDiscountActive" :notice-text="'Bakery discount of ' . $store_resource->store_value_modifier_as_percentage . ' % is active'" />
    </x-helpSection>
    <x-store.storeContainer :store-resource="$store_resource" :options="[
        'item-requirements' => true,
        'item-information' => true,
    ]" />
</div>
