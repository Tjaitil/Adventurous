<h1 class="page_title">{{ $title }}</h1>
<div id="bakery">
    <x-helpSection>
        <p>Here you can make food to decrease your hunger.
            <br> For more information head to <a href="gameguide/bakery"
                target="_blank">gameguide/bakery</a>
        </p>
        <x-profiencyBenefitNotice :is-active="$store_resource->store_value_modifier > 0" :notice-text="'Discount of ' .
            $store_resource->store_value_modifier_as_percentage .
            ' % is active'" />
    </x-helpSection>
    <x-store.storeContainer :store-resource="$store_resource" :options="[
        'item-requirements' => true,
        'item-information' => true,
    ]" />
</div>
