<x-pageTitle>
    Workforce Lodge
</x-pageTitle>
<div id="workers">
    <div id="workers-overview" class="flex gap-2">
        @php
            $new_farmer_workers_amount = $maxFarmerWorkers - $FarmerWorkforce->workforce_total;
            $new_farmer_worker_amount = $new_farmer_workers_amount > 0 ?: 0;
            $new_mine_workers_amount = $maxMinerWorkers - $MinerWorkforce->workforce_total;
            $new_mine_worker_amount = $new_mine_workers_amount > 0 ?: 0;
        @endphp
        <div class="flex flex-row justify-center gap-3">
            <div id="farmer_workers" class="w-1/2">
                <h4>Farmer workforce</h4>
                <p>
                    Current efficiency level:
                    <span id="farmer-efficiency-level-el">
                        {{ $FarmerWorkforce->efficiency_level }}
                    </span>
                </p>
                <x-baseTable>
                    <x-slot name="body">
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Total workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $FarmerWorkforce->workforce_total }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Towhar workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $FarmerWorkforce->towhar }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Krasnur workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $FarmerWorkforce->krasnur }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Available workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $FarmerWorkforce->avail_workforce }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Max farmer workers
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $maxFarmerWorkers }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Can recruit
                            </x-baseTableCol>
                            <x-baseTableCol>
                                <span @class(['text-green-700' => $new_farmer_worker_amount > 0])>
                                    {{ $new_farmer_worker_amount }}
                                </span>
                            </x-baseTableCol>
                        </x-baseTableRow>
                    </x-slot>
                </x-baseTable>
            </div>
            <div id="miner_workers" class="w-1/2">
                <h4>Miner workforce</h4>
                <p>
                    Current efficiency level:
                    <span id="miner-efficiency-level-el">
                        {{ $MinerWorkforce->efficiency_level }}
                    </span>
                </p>
                <x-baseTable>
                    <x-slot name="body">
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Total workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $MinerWorkforce->workforce_total }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Golbak workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $MinerWorkforce->golbak }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Snerpiir workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $MinerWorkforce->snerpiir }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Available workforce
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $MinerWorkforce->avail_workforce }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Max miner workers
                            </x-baseTableCol>
                            <x-baseTableCol>
                                {{ $maxMinerWorkers }}
                            </x-baseTableCol>
                        </x-baseTableRow>
                        <x-baseTableRow>
                            <x-baseTableCol>
                                Can recruit
                            </x-baseTableCol>
                            <x-baseTableCol>
                                <span @class(['text-green-700' => $new_mine_worker_amount > 0])>
                                    {{ $new_mine_worker_amount }}
                                </span>
                            </x-baseTableCol>
                        </x-baseTableRow>
                    </x-slot>
                </x-baseTable>
            </div>
        </div>
    </div>
    <x-borderInterfaceContainer>
        <h2>Upgrade workers efficiency</h2>
        <p>Higher efficiency means lower time when mining or growing</p>
        <form action="javascript:void(0)" id="efficiency-upgrade-form" class="flex flex-col gap-3">
            <div>
                <x-baseRadio name="efficiency-upgrade-profiency" id="efficiency-upgrade-farmer"
                    value="farmer" data-efficiency-upgrade-profiency="farmer" :checked="true"
                    :data-efficiency-level="$FarmerWorkforce->efficiency_level + 1" :data-efficiency-upgrade-cost="$farmer_efficiency_cost">
                    Farmer
                </x-baseRadio>
                <x-baseRadio name="efficiency-upgrade-profiency" id="efficiency-upgrade-miner"
                    value="miner" data-efficiency-upgrade-profiency="miner" :data-efficiency-level="$MinerWorkforce->efficiency_level"
                    :data-efficiency-upgrade-cost="$miner_efficiency_cost">
                    Miner
                </x-baseRadio>
            </div>
            <div id="efficiency-upgrade-info" class="">
                <p>Current efficiency level:
                    <span
                        id="upgrade-info-efficiency-level-el">{{ $FarmerWorkforce->efficiency_level + 1 }}</span>
                </p>
                <p class="my-0">Upgrade cost</p>
                <x-goldCost :amount="$farmer_efficiency_cost" />
            </div>
            <x-button buttonText="Upgrade efficiency" type="submit">
                Upgrade efficiency
            </x-button>
        </form>
    </x-borderInterfaceContainer>
</div>
