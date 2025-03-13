<div id="sidebar" class="h-full pt-2 bg-primary-800 px-2 text-white transition-all duration-200">
    <button id="sidebar_button_toggle"
        class="sidebar_button rounded-md text-black bg-orange-50 border-2 shadow p-2
        font-bold text-xs float-right invisible outline-hidden">
        {{ '<<' }} </button>
    <p>{{ ucfirst($username) }}</p>
    <p class="mb-1 mt-1">{{ ucfirst($profiency) }}</p>
    <p>{{ ucwords($location) }}</p>
    <div class="flex flex-row gap-2">
        <x-tabList data-is-setup="true" class="flex flex-col max-w-[100px]">
            <x-tab id="log-tab" aria-controls="sidebar-log-tabpanel" class="sidebar-tab">
                Log
            </x-tab>
            <x-tab id="adventure-tab" aria-controls="sidebar-adventure-tabpanel" class="sidebar-tab">
                Adventure
            </x-tab>
            <x-tab id="countdown-tab" aria-controls="sidebar-countdowns-tabpanel" class="sidebar-tab">
                Countdowns
            </x-tab>
            <x-tab id="diplomacy-tab" aria-controls="sidebar-diplomacy-tabpanel" class="sidebar-tab">
                Diplomacy
            </x-tab>
            <x-tab id="skills-tab" aria-controls="sidebar-skills-tabpanel" class="sidebar-tab">
                Skills
            </x-tab>
            <x-tab id="help-tab" aria-controls="help-settings-tabPanel" class="sidebar-tab">
                <img class="mx-auto w-12 h-12" id="HUD_help_button" src="{{ asset('images/help icon.png') }}" />
            </x-tab>
            <x-tab id="client-settings-tab" aria-controls="sidebar-settings-tabPanel" class="sidebar-tab">
                <img class="mx-auto w-12 h-12" id="setting_button" src="{{ asset('images/settings icon.png') }}" />
            </x-tab>
        </x-tabList>
        <div id="sidebar-tabpanels" class="grow overflow-y-scroll h-full max-h-[600px]">
            <x-tabPanel id="sidebar-log-tabpanel" aria-labelled-by="log-tab">
                <x-client.gameLog :messages="$gameLog" />
            </x-tabPanel>
            <x-tabPanel id="sidebar-adventure-tabpanel" aria-labelled-by="adventure-tab">
                {{-- Add adventure tab here --}}
            </x-tabPanel>
            <x-tabPanel id="sidebar-countdowns-tabpanel" aria-labelled-by="countdown-tab">
                <x-profiencyStatus.profiencyStatusContainer :profiency-status="$profiency_status" />
            </x-tabPanel>
            <x-tabPanel id="sidebar-diplomacy-tabpanel" aria-labelled-by="diplomacy-tab">
                <?php
                // echo TemplateFetcher::loadTemplate('diplomacy', $data['diplomacy_data'])
                ?>
            </x-tabPanel>
            <x-tabPanel id="sidebar-skills-tabpanel" aria-labelled-by="skills-tab">
                <div class="vue-app">
                    <skill-info-list :init-levels="{{ json_encode($Levels) }}" />
                </div>
            </x-tabPanel>
            <x-tabPanel id="help-settings-tabPanel" aria-labelled-by="help-settings-tab">
                @include('partials.clientHelp')
            </x-tabPanel>
            <x-tabPanel id="sidebar-settings-tabPanel" aria-labelled-by="client-settings-tab">
                @include('partials.clientSettings')
            </x-tabPanel>
        </div>
    </div>
</div>
