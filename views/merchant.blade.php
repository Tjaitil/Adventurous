<h1 class="page_title">Merchant</h1>
<div id="trades">
    <p id="trades_countdown">New merchant offers in <span
            id="trades_countdown_time"></span></p>
    <x-helpSection>
        Beware that diplomacy relation to
        places can affect prices. Difference will be displayed in
        parenthesis. Trading in adventure locations will affect
        diplomacy relation
    </x-helpSection>
    <x-store.storeContainer :store-resource="$store_resource" :options="[
        'item_requirements' => false,
        'item_information' => false,
        'show-input-amount' => true,
        'show-requirements' => false,
    ]" />
    <p id="trade_info" class="mb-1 mt-1">
        Click on shop item to buy or click on inventory item to
        sell
        item. The store prices are listed as buy price / sell
        price.
        The merchant will only accept trading on items it is
        already
        selling. Head to Fagna to sell all items.
    </p>
</div>
<div id="trader_assignments">
    <x-trader.currentTraderAssignment :current-assignment="$CurrentAssignment" :trader="$Trader" />
    <p id="trader_assignments_countdown">New trader assignments
        in
        <span id="trader_assignments_countdown_time"></span>
    </p>
    <h3 class="text-lg">Select your assignment below. Greyed out
        assignments are locked</h3>
    <div class="mb-4">
        <h4 class="font-bold">Assignments available in this
            location
        </h4>
        <button type="button" id="start_trader_assignment" class="mb-1 mt-1">Do
            Assigment</button>
        <div id="trader_assignments_container ">
            <x-trader.traderAssignmentsList :assignments="$CurrentLocationAssignments" :current-location="$current_location"
                :trader-level="$trader_level" />
        </div>
    </div>
    <div>
        <h3 class="font-bold">Assignments available in other
            locations</h3>
        <div id="trader_assignments_container">
            <x-trader.traderAssignmentsList :assignments="$OtherAssignments" :current-location="$current_location"
                :trader-level="$trader_level" />
        </div>
    </div>
</div>
