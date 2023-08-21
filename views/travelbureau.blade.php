            <h1 class="page_title">{{ $title }}</h1>
            <div id="cart_shop">
                <div class="mb-2">
                    <p class="mb-0">Your current cart</p>
                    @component('components.item', [
                        'name' => $current_cart->name,
                        'show_tooltip' => false,
                        'id' => 'current-cart',
                        'show_amount' => false,
                    ])
                        ;
                    @endcomponent
                </div>
                @component('components.storeContainer', [
                    'store_resource' => $store_resource,
                    'options' => [
                        'item_requirements' => true,
                        'item_information' => true,
                        'input_amount' => false,
                    ],
                ])
                @endcomponent
            </div>
