@php
    /**
     * @param string $skill
     * @param string $imgSrc
     * @param int $level
     * @param int $experience
     */
@endphp
<div class="skill-level-wrapper relative w-1/2 max-w-[80px] border-2 border-black bg-orange-50 text-black"
    data-wrapper-skill="{{ $skill }}">
    <x-skillIcon :skill="$skill" :level="$level" :has-required-level="false"
        :show-able-color="false" :size="'medium'" />
    <span class="skill_tooltip absolute border-black border-2 bottom-0 left-0 shadow-2xl float-right bg-orange-50 text-black font-bold text-xs text-center
        w-auto p-1 z-20 inline-block"
    ">Current
        experience {{ number_format($experience) }}</span>
</div>
