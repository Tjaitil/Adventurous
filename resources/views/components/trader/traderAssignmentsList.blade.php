@props(['assignments' => [], 'currentLocation', 'traderLevel'])
@php
    /**
     *
     * @param $assigments TraderAssignment[]
     * @param string $currentLocation
     * @param int $traderLevel
     */
@endphp
<div id="trader_assignments_container "
    class="grid grid-cols-[repeat(auto-fit,180px)] justify-center gap-4">
    @forelse ($assignments as $value)
        @php
            $has_required_level = $traderLevel >= $value->type->required_level;
            $not_current_location = $value->base !== $currentLocation;
        @endphp
        <div @class([
            'flex flex-col justify-center max-w[180px] justify-center pb-2 trader_assignment text-white',
            'div_content',
            'div_content_dark',
            'grayscale-[60%]' => $not_current_location || !$has_required_level,
        ])>
            <div class="mb-2 flex items-center justify-center gap-2">
                <x-item :name="$value->cargo" :show-tooltip="false" :show-amount="false" />
                <span class="align-middle">
                    {{ ' X ' . $value->assignment_amount }}
                </span>
            </div>
            <p @class(['not-able-color' => $not_current_location])>
                {{ ucfirst($value->base) . ' ' . '->' . ' ' . ucfirst($value->destination) }}
            </p>
            <x-skillIcon :skill="\App\Enums\SkillNames::TRADER->value" :show-able-color="true" :level="$value->type->required_level"
                :has-required-level="$has_required_level" />
            <p>
                {{ ucwords($value->assignment_type) }}
            </p>
            <span class="trader_assignment_id hidden">{{ $value->id }}</span>
        </div>
    @empty
        <p>No assignments</p>
    @endforelse
</div>
