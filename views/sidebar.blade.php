<div id="sidebar"
    class="fixed min-h-[500px] w-2/12 overflow-y-scroll bg-primary-800 px-2 text-white transition-all duration-200">
    <button id="sidebar_button_toggle" class="sidebar_button">
        {{ '<<' }} </button>
    <p>{{ ucfirst($username) }}</p>
    <p class="mb-1 mt-1">{{ ucfirst($profiency) }}</p>
    <p>{{ ucwords($location) }}</p>

    <x-tab id="sidebar-tab-1" tab-group="sidebar-tab" tab-text="Adventure"
        target-id="sidebar-adventure-tabpanel" />
    <x-tab id="sidebar-tab-2" tab-group="sidebar-tab" tab-text="Countdowns"
        target-id="sidebar-countdowns-tabpanel" />
    <x-tab id="sidebar-tab-3" tab-group="sidebar-tab" tab-text="Diplomacy"
        target-id="sidebar-diplomacy-tabpanel" />
    <x-tab id="sidebar-tab-4" tab-group="sidebar-tab" tab-text="Skills"
        target-id="sidebar-skills-tabpanel" />
    <div id="sidebar-tabpanels">
        <x-tabpanel id="sidebar-adventure-tabpanel" tab-id="sidebar-tab-1">
            {{-- Add adventure tab here --}}
        </x-tabpanel>
        <x-tabpanel id="sidebar-countdowns-tabpanel" tab-id="sidebar-tab-2">
            <x-profiencyStatus.profiencyStatusContainer :profiency-status="$profiency_status" />
        </x-tabpanel>
        <x-tabpanel id="sidebar-diplomacy-tabpanel" tab-id="sidebar-tab-3">
            <?php
            // echo TemplateFetcher::loadTemplate('diplomacy', $data['diplomacy_data'])
            ?>
        </x-tabpanel>
        <x-tabpanel id="sidebar-skills-tabpanel" tab-id="sidebar-tab-4">
            <x-skillLevels.skillLevelsContainer :levels="$levels" />
        </x-tabpanel>
    </div>
</div>
