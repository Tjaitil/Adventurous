@props(['assignment', 'trader'])
@php
    /**
     *
     * @var \App\models\TraderAssignment $CurrentAssignment
     * @var \App\models\Trader $Trader
     */
@endphp
<div id="traderAssignment_current" class="mb-2">
    <x-borderInterfaceContainer>
        <div class="grid-cols grid gap-4">
            <p class="col-span-2 mb-0">Current trader assignment details</p>
            @if (isset($Trader->traderAssignment->id))
                <div class="col-span-2">
                    <x-item :name="$Trader->traderAssignment->cargo" :show-tooltip="false"
                        :show-toolip="false" :show-amount="false" />
                </div>
                <div>
                    Route<br>
                    <span>
                        {{ ucwords($CurrentAssignment->base) . ' ' . '->' . ' ' . ucwords($CurrentAssignment->destination) }}
                    </span>
                </div>
                <div>
                    <span class="mb-2">Progress</span>
                    <x-progressBar id="traderAssignment_progressBar"
                        :current-value="$Trader->delivered" :max-value="$CurrentAssignment->assignment_amount" />
                </div>
                <div>
                    Assignment type<br>
                    <span>
                        {{ $CurrentAssignment->assignment_type }}
                    </span>
                </div>
                @php
                    $hasCapasity = $Trader->cart_amount < $Trader->cart->capasity && $Trader->cart_amount + $Trader->delivered < $CurrentAssignment->assignment_amount;
                @endphp
                <div id="traderAssignment-cart-amount-wrapper">
                    Cart Capasity<br>
                    <span
                        id="traderAssignment-cart-amount">{{ $Trader->cart_amount }}</span>
                    /
                    <span>{{ $Trader->cart->capasity }}</span>
                </div>
                <div class="col-span-2 mb-4">
                    @if ($hasCapasity)
                        <x-button id="traderAssignment-pick-up"
                            text="Pick up items" />
                    @else
                        <x-button id="traderAssignment-pick-up"
                            text="Pick up items" />
                    @endif
                </div>
            @else
                <div class="col-span-2">
                    No current assignment, to get new assignment see the
                    assignment list
                </div>
            @endif
        </div>
    </x-borderInterfaceContainer>
</div>
