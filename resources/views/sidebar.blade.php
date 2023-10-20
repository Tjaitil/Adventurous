<div id="sidebar"
    class="fixed min-h-[500px] w-2/12 overflow-y-scroll bg-primary-800 px-2 text-white transition-all duration-200">
    <button id="sidebar_button_toggle" class="sidebar_button">
        {{ '<<' }} </button>
    <p>{{ ucfirst($username) }}</p>
    <p class="mb-1 mt-1">{{ ucfirst($profiency) }}</p>
    <p>{{ ucwords($location) }}</p>

    <div>
        <x-tab id="sidebar-tab-1" aria-controls="sidebar-adventure-tabpanel" class="sidebar-tab">
            Adventure
        </x-tab>
        <x-tab id="sidebar-tab-2" aria-controls="sidebar-countdowns-tabpanel" class="sidebar-tab">
            Countdowns
        </x-tab>
        <x-tab id="sidebar-tab-3" aria-controls="sidebar-diplomacy-tabpanel" class="sidebar-tab">
            Diplomacy
        </x-tab>
        <x-tab id="sidebar-tab-4" aria-controls="sidebar-skills-tabpanel" class="sidebar-tab">
            Skills
        </x-tab>
    </div>
    <div id="sidebar-tabpanels">
        <x-tabpanel id="sidebar-adventure-tabpanel" aria-labelled-by="sidebar-tab-1">
            {{-- Add adventure tab here --}}
        </x-tabpanel>
        <x-tabpanel id="sidebar-countdowns-tabpanel" aria-labelled-by="sidebar-tab-2">
            <x-profiencyStatus.profiencyStatusContainer :profiency-status="$profiency_status" />
        </x-tabpanel>
        <x-tabpanel id="sidebar-diplomacy-tabpanel" aria-labelled-by="sidebar-tab-3">
            <?php
            // echo TemplateFetcher::loadTemplate('diplomacy', $data['diplomacy_data'])
            ?>
        </x-tabpanel>
        <x-tabpanel id="sidebar-skills-tabpanel" aria-labelled-by="sidebar-tab-4">
            <x-skillLevels.skillLevelsContainer :levels="$levels" />
        </x-tabpanel>
    </div>
</div>
