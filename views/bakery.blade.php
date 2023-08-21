#|bakery.js|
<h1 class="page_title">Bakery</h1>
<div id="bakery">
    @component('components.help')
        <p>Here you can make food to decrease your hunger.
            <br> For more information head to <a href="gameguide/bakery" target="_blank">gameguide/bakery</a>
        </p>
        @component('components.profiencyBenefitNotice', [
            'is_active' => $store_resource->store_value_modifier > 0,
            'notice_text' => "Discount of $store_resource->store_value_modifier_as_percentage % is active",
        ])
        @endcomponent
    @endcomponent
    @component('components.storeContainer', [
        'store_resource' => $store_resource,
        'options' => [
            'item_requirements' => true,
            'item_information' => true,
        ],
    ])
    @endcomponent
</div>
