    <link href="{{ asset('css/advclient.css') }}" rel="stylesheet">
    @vite(['resources/js/clientScripts/inventory.ts', 'resources/js/clientScripts/getXp.ts', 'resources/js/clientScripts/sidebar.ts'])
    @extends('layoutSectionAside')
    @section('title', 'Adventurous')
    @section('sectionContent')
        <div class="log_3"></div>
        <div id="client-loading-container">
            <h1>Loading client</h1>
            <h1>...</h1>
        </div>

        <div id="client-container">
            @include('layout')
            @include('partials.conversationContainer')
            @include('partials.gameScreen')
            <div id="client_help_container" class="div_content div_content_dark">
                <div id="client_help_content">
                    <img class="cont_exit" src="{{ asset('images/exit.png') }}" />
                    <h1 class="page_title"> Help </h1>
                    </br>
                    <div id="help_content_introduction" class="mb-1">
                        Here is some short information to help you out. More extensive info can be found
                        on /gameguide.
                    </div>
                    <div class="help_content_section">
                        <h4 class="help_content_section">Hunger</h4>
                        <p class="help_content_section_p">
                            Every time you grow crops, start mining, start a new assignment or a new
                            army mission the hunger
                            will increase. To decrease hunger e.g increase hunger bar you have to eat
                            food. Visit tavern found
                            in every city to eat.
                        </p>
                    </div>
                </div>
            </div>
            @include('partials.clientSettings')
            <div id="game_hud">
                <x-progressBar id="hunger_progressBar" :current-value="$Hunger->current" :max-value="$Hunger->max" />
                <x-progressBar id="health_progressBar" :current-value="100" :max-value="100" />
                <img src="{{ asset('/images/hunted icon.png') }}" id="HUD_hunted_icon" />
                <p id="HUD_hunted_locater"></p>
                <div id="HUD-left-icons-container">

                    <img class="HUD-icon" id="toggle_map_icon" src="{{ asset('images/globe.png') }}" />
                    <img class="HUD-icon" id="HUD_help_button"
                        src="{{ asset('images/help icon.png') }}" />
                    <img class="HUD-icon" id="setting_button"
                        src="{{ asset('images/settings icon.png') }}" />
                </div>
                <div id="control_text">
                    <p class="extendedControls">C - Toggle Attack Mode</p>
                    <p class="extendedControls">P - Pause</p>
                    <p>A - Attack</p>
                    <p id="control_text_building">E -</p>
                    <p id="control_text_conversation">W -</p>
                </div>
            </div>
            <div id="game_text">
            </div>
            @include('partials.gameMap');
            <div id="inv_toggle_button_container">
                <button id="inv_toggle_button">INV</button>
            </div>
            <div id="inventory">
                @include('inventory', ['inventory' => $Inventory])
            </div>
            <div id="control">
                <button id="control_button"></button>
            </div>
        </div>
    @endsection
    @section('asideContent')
        @include('sidebar')
    @endsection
