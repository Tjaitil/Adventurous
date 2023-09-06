            <h1 class="page_title">{{ $title }}</h1>
            <div id="cart_shop">
                <div class="mb-2">
                    <p class="mb-0">Your current cart</p>
                    <x-item :name="$current_cart->name" :show-tooltip="false"
                        :show-amount="false" id="current-cart" />
                </div>
                <x-store.storeContainer :store-resource="$store_resource" :options="[
                    'item_requirements' => true,
                    'item_information' => true,
                    'input_amount' => false,
                ]" />
            </div>
