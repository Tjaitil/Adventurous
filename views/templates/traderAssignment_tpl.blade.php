@php
    /**
     *
     * @var \App\models\TraderAssignment $CurrentAssignment
     * @var \App\models\Trader $Trader
     */
@endphp
<div id="traderAssignment_current" class="mb-2">
    @component('components.BorderInterfaceContainer')
        <div class="grid gap-4 grid-cols">
            <p class="mb-0 col-span-2">Current trader assignment details</p>
            @if (isset($Trader->traderAssignment->id))
                <div class="col-span-2">
                    @component('components.item', [
                        'name' => $CurrentAssignment->cargo,
                        'show_tooltip' => false,
                    ])
                    @endcomponent
                </div>
                <div>
                    Route<br>
                    <span>
                        {{ ucwords($CurrentAssignment->base) . ' ' . '->' . ' ' . ucwords($CurrentAssignment->destination) }}
                    </span>
                </div>
                <div>
                    <span class="mb-2">Progress</span>
                    @component('components.progressBar', [
                        'id' => 'traderAssignment_progressBar',
                        'current_value' => $Trader->delivered,
                        'max_value' => $CurrentAssignment->assignment_amount,
                    ])
                    @endcomponent
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
                    <span id="traderAssignment-cart-amount">{{ $Trader->cart_amount }}</span> /
                    <span>{{ $Trader->cart->capasity }}</span>
                </div>
                <div class="col-span-2 mb-4">
                    @if ($hasCapasity)
                        @component('components.button', ['id' => 'traderAssignment-pick-up', 'text' => 'Pick up items'])
                        @endcomponent
                    @else
                        @component('components.button', ['id' => 'traderAssignment-deliver', 'text' => 'Deliver'])
                        @endcomponent
                    @endif
                </div>
            @else
                <div class="col-span-2">
                    No current assignment, to get new assignment see the assignment list
                </div>
            @endif
        </div>
    @endcomponent
</div>
