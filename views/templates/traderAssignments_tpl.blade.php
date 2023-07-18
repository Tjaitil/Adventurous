@php
    /**
     *
     * @var \App\models\TraderAssignment $CurrentAssignment
     * @var \App\models\Trader $Trader
     */
@endphp
<div id="trader_assignments_container " class="grid grid-cols-[repeat(auto-fit,_180px)] gap-4 justify-center">
    @forelse ($Assignments as $value)
        @php
            $has_required_level = $trader_level >= $value->type->required_level;
            $not_current_location = $value->base !== $current_location;
        @endphp
        <div @class([
            'flex flex-col justify-center max-w[180px] justify-center pb-2 trader_assignment text-white',
            'div_content',
            'div_content_dark',
            'grayscale-[60%]' => $not_current_location || !$has_required_level,
        ])>
            <div class="flex justify-center items-center gap-2 mb-2">
                @component('components.item', [
                    'name' => $value->cargo,
                    'show_tooltip' => false,
                ])
                @endcomponent
                <span class="align-middle">
                    {{ ' X ' . $value->assignment_amount }}
                </span>
            </div>
            <p @class(['not-able-color' => $not_current_location])>
                {{ ucfirst($value->base) . ' ' . '->' . ' ' . ucfirst($value->destination) }}
            </p>
            @component('components.skillIcon', [
                'skill' => \TRADER_SKILL_NAME,
                'show_able_color' => true,
                'level' => $value->type->required_level,
                'has_required_level' => $has_required_level,
            ])
            @endcomponent
            <p>
                {{ ucwords($value->assignment_type) }}
            </p>
            <span class="hidden trader_assignment_id">{{ $value->id }}</span>
        </div>
    @empty
        <p>No assignments</p>
    @endforelse
</div>
