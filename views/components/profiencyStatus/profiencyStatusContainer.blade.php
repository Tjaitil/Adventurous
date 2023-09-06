@props(['profiency-status'])
@php
    $Farmers = $profiencyStatus['Farmers'];
    $Miners = $profiencyStatus['Miners'];
    $Trader = $profiencyStatus['Trader'];
    $warrior_statuses = $profiencyStatus['warrior_statuses'];
    /**
     * @var array $profiencyStatus
     *
     */
@endphp
<div class="text-white">
    <x-profiencyStatus.profiencyStatusWrapper>
        <img class="mx-auto"
            src="{{ constant('ROUTE_IMG') . 'farmer icon.png' }}" />
        @foreach ($Farmers as $Farmer)
            <h3 class="mt-1">{{ ucwords($Farmer->location) }}</h3>
            <p>
                @if ($Farmer->crop_type && Carbon::now()->isBefore($Farmer->crop_countdown))
                    Finished in
                    {{ $Farmer->crop_countdown->diffInMinutes(Carbon::now()) }}
                @elseif($Farmer->crop_type && Carbon::now()->isAfter($Farmer->crop_countdown))
                    Finished
                    @el
                    se
                    Nothing happening
                @endif
            </p>
        @endforeach
    </x-profiencyStatus.profiencyStatusWrapper>
    <x-profiencyStatus.profiencyStatusWrapper>

        <img class="mx-auto"
            src="{{ constant('ROUTE_IMG') . 'miner icon.png' }}" />
        @foreach ($Miners as $Miner)
            <h3 class="mt-1">{{ ucwords($Miner->location) }}</h3>
            <p>
                @if ($Miner->mineral_type && Carbon::now()->isBefore($Miner->mining_countdown))
                    Finished in
                    {{ $Miner->mining_countdown->diffInMinutes(Carbon::now()) }}
                @elseif($Miner->mineral_type && Carbon::now()->isAfter($Miner->mining_countdown))
                    Finished
                @else
                    Nothing happening
                @endif
            </p>
        @endforeach
    </x-profiencyStatus.profiencyStatusWrapper>
    <x-profiencyStatus.profiencyStatusWrapper>
        <img class="mx-auto"
            src="{{ constant('ROUTE_IMG') . 'trader icon.png' }}" />
        @if (intval($Trader->assignment_id === 0))
            Nothing happening
        @else
            <h3>Current assignment</h3>
            <div class="grid-cols grid gap-4 px-2">
                <div class="col-span-2">
                    <x-item :name="$Trader->traderAssignment->cargo" :show-tooltip="false"
                        :show-amount="false" />
                </div>
                <div>
                    Route<br>
                    <span>
                        {{ ucwords($Trader->traderAssignment->base) . ' ' . '->' . ' ' . ucwords($Trader->traderAssignment->destination) }}
                    </span>
                </div>
                <div>
                    <span class="mb-2">Progress</span>
                    <x-progressBar :id="'traderAssignment_progressBar'" :current_value="$Trader->delivered"
                        :max_value="$Trader->traderAssignment->assignment_amount" />
                </div>
                <div>
                    Assignment type<br>
                    {{ $Trader->traderAssignment->assignment_type }}
                </div>
                <div>
                    {{ ucwords($Trader->traderAssignment->base) . ' -->' . ucwords($Trader->traderAssignment->destination) }}
                </div>
                <div>
                    Cart Capasity:
                    {{ $Trader->cart_amount . ' / ' . $Trader->cart->capasity }}
                </div>
            </div>
        @endif
    </x-profiencyStatus.profiencyStatusWrapper>
    <x-profiencyStatus.profiencyStatusWrapper>
        <img class="mx-auto"
            src="{{ constant('ROUTE_IMG') . 'warrior icon.png' }}" />
        <h3>Warrior(s)</h3>
        <p>{{ 'finished training: ' . $warrior_statuses['statuses']['finished_training'] }}
        </p>
        <p>{{ 'training: ' . $warrior_statuses['statuses']['training'] }}</p>
        <p>{{ 'on mission: ' . $warrior_statuses['statuses']['on_mission'] }}
        </p>
        <p>{{ 'resting: ' . $warrior_statuses['statuses']['resting'] }}</p>
        <p>{{ 'idle: ' . $warrior_statuses['statuses']['idle'] }}</p>
        <h3>Armymissions</h3>
        <?php
        // foreach ($data['warrior_status']['army_mission']['current_army_missions'] as $key) :
        ?>
        <p><?php
        // echo $key['countdown'];
        ?></p>
        <?php
        // endforeach;
        ?>
    </x-profiencyStatus.profiencyStatusWrapper>
</div>
