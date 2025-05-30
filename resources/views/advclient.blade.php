    @vite(['resources/js/clientScripts/inventory.ts', 'resources/js/clientScripts/sidebar.ts'])
    @extends('layoutSectionAside')
    @section('title', 'Adventurous')
    @section('sectionContent')
        <div id="client-container" class="flex flex-row gap-x-4 opacity-0 transition-opacity duration-500 ease-in">
            <div class="grow relative">
                @include('layout')
                <x-client.logModal />
                <div class="vue-app">
                    <conversation-container />
                </div>
                @include('partials.gameScreen')
                <div id="game_hud" class="z-10 absolute">
                    <x-progressBar id="hunger_progressBar" :current-value="$Hunger->current" :max-value="100" />
                    <x-progressBar id="health_progressBar" :current-value="100" :max-value="100" />
                    <img src="{{ asset('/images/hunted icon.png') }}" id="HUD_hunted_icon" class="absolute hidden" />
                    <p id="HUD_hunted_locater" class="absolute text-white"></p>
                    <div id="HUD-left-icons-container" class="flex absolute top-[10px]">
                        <img class="cursor-pointer" id="toggle_map_icon" src="{{ asset('images/globe.png') }}" />

                    </div>
                    <div id="control_text" class="text-white absolute">
                        <p class="text-left my-0 extendedControls">C - Toggle Attack Mode</p>
                        <p class="text-left my-0 extendedControls">P - Pause</p>
                        <p class="text-left my-0">A - Attack</p>
                        <p id="control_text_building" class="text-left my-0">E -</p>
                        <p id="control_text_conversation" class="text-left my-0">W -</p>
                    </div>
                </div>
                <div id="game_text" class="absolute text-white text-left">
                </div>
                <div id="inv_toggle_button_container">
                    <button id="inv_toggle_button">INV</button>
                </div>
                <div id="control" class="invisble absolute">
                    <button id="control_button"></button>
                </div>
                <input type="checkbox" name="" id="draw_checkbox">
            </div>
            <div class="vue-app w-[29%]">
                <inventory-container />
            </div>
        </div>
        <x-items.itemTooltip />
        @include('partials.gameMap')
    @endsection
    @section('asideContent')
        @include('sidebar')
    @endsection
