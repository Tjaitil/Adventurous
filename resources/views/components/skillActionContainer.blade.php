@props([
    'showPermits',
    'workforceData',
    'doActionText',
    'finishActionText',
    'cancelActionText',
    'actionTypeLabel',
    'actionItems' => [],
    'permits' => 0,
])
@php
    /**
     * @param bool $showPermits
     * @param string $doActionText
     * @param string $finishActionText
     * @param string $cancelActionText
     * @param string $actionTypeLabel
     * @param array $actionItems
     * @param array $workforceData
     */
@endphp
<x-borderInterfaceContainer border-style='orange'>
    <div id="skill-action-container" class="bg-orange-50 text-black">
        <div id="actions" class="border-b-2 border-black py-2">
            <p id="info-action-element"></p>
            <p id="time"></p>
            <x-button id="cancel-action" class="hidden">
                {{ $cancelActionText }}
            </x-button>
            <x-button id="finish-action" class="hidden">
                {{ $finishActionText }}
            </x-button>
        </div>
        <div id="action_body" class="flex flex-col md:flex-row">
            <div class="w-1/2">
                <div id="select" class="grid grid-cols-4 p-2">
                    @foreach ($actionItems as $item)
                        @php
                            $type = isset($item['crop_type']) ? $item['crop_type'] : $item['mineral_type'] . ' ore';
                        @endphp
                        <img class="item-type m-auto h-16 w-16" src="{{ asset('images/' . $type . '.png') }}"
                            alt="{{ $type }}" />
                    @endforeach
                </div>
            </div>
            <div id="data_container" class="w-1/2 border-l-2 border-black px-1">
                <div id="data" class="invisible">
                    @if ($showPermits)
                        <p class="mt-4"> Your total permits: <span id="total_permits">
                                {{ $permits }} </span></p>
                    @endif
                    <figure id="selected_item"></figure>
                    <form id="data_form" class="flex flex-col gap-4 p-2">
                        <x-baseInput id="selected-action-type" name="action-type" :labelText="$actionTypeLabel" type="text"
                            value="" :show-label="true" />
                        <x-baseInput id="time" name="time" labelText="Time" type="text" value=""
                            :show-label="true" />
                        <div>
                            <span class="block">Efficiency level reduction</span>
                            <span id="reduction_time"></span>
                        </div>
                        <x-baseInput id="location" name="location" labelText="Location" type="text" value=""
                            :show-label="true" />
                        <x-baseInput id="level" name="level" labelText="Level" type="text" value=""
                            :show-label="true" />
                        <x-baseInput id="experience" name="experience" labelText="Experience" type="text"
                            value="" :show-label="true" />
                        @if ($showPermits)
                            <x-baseInput id="permits" name="permits" labelText="Permit costs" type="text"
                                value="" :show-label="true" />
                        @else
                            <x-baseInput id="seeds" name="seeds" labelText="Seeds" type="text" value=""
                                :show-label="true" />
                        @endif
                        <x-baseInput id="workforce_amount" name="workforce_amount" labelText="Select workers (max)"
                            type="number" value="" :show-label="true">
                            <span id="data_container_avail_workforce">
                                ( {{ $workforceData['avail_workforce'] }} )
                            </span>
                        </x-baseInput>
                        <div class="col-span-2">
                            <x-button id="do-action">
                                {{ $doActionText }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-borderInterfaceContainer>
