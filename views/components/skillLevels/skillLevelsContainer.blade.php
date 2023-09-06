@props(['levels'])
@php
    /**
     * @param array $levels
     */
@endphp
<div id="skill-levels-container" class="mt-1 flex flex-row flex-wrap">
    <x-skillLevels.skillLevelWrapper :skill="'adventurer'" :level="$levels['adventurer_respect']"
        :experience="$levels['adventurer_respect']" :img="'adventurer icon'" />
    <x-skillLevels.skillLevelWrapper :skill="'farmer'" :level="$levels['farmer_level']"
        :experience="$levels['farmer_xp']" :img="'farmer icon'" />
    <x-skillLevels.skillLevelWrapper :skill="'miner'" :level="$levels['miner_level']"
        :experience="$levels['miner_xp']" :img="'miner icon'" />
    <x-skillLevels.skillLevelWrapper :skill="'trader'" :level="$levels['trader_level']"
        :experience="$levels['trader_xp']" :img="'trader icon'" />
    <x-skillLevels.skillLevelWrapper :skill="'warrior'" :level="$levels['warrior_level']"
        :experience="$levels['warrior_xp']" :img="'warrior icon'" />
</div>
