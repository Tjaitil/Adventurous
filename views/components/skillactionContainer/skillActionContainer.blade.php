<?php

/**
 * @var array $data
 * @property string $do_action_text
 * @property string $finish_action_text
 * @property string $cancel_action_text
 * @property string $action_type_label
 * @property array $action_types
 * @property bool $show_permits
 */
?>
<div id="action_div" class="div_content">
    <div id="actions" class>
        <p id="info-action-element"></p>
        <p id="time"></p>
        <x-button :id="'canecl-action'" :text="$cancel_action_text" />
        <x-button :id="'finish_action'" :text="$finish_action_text" />
    </div>
    <div id="action_body">
        <div id="select">

            @foreach ($action_items as $item)
                @php
                    $type = isset($item['crop_type']) ? $item['crop_type'] : $item['mineral_type'] . ' ore';
                @endphp

                <img class="item-type"
                    src="{{ constant('ROUTE_IMG') . $type . '.png' }}"
                    alt="{{ $type }}" />
            @endforeach
        </div>
        <div id="data_container" class="px-1">
            <div id="data">
                @if ($data['show_permits'])
                    <p> Your total permits: <span id="total_permits">
                            {{ $permits }} </span></p>
                @endif
                <figure id="selected_item"></figure>
                <form id="data_form">
                    <label for="action-type"> {{ $action_type_label }}</label>
                    <input type="text" id="selected-action-type"
                        name="action-type" readonly />
                    <label for="time"> Time </label>
                    <input type="text" name="time" readonly />
                    <span>Efficiency reduction</span><span
                        id="reduction_time"></span>
                    <label for="location">Location</label>
                    <input type="text" name="location" readonly />
                    <label for="level">Level</label>
                    <input type="text" name="level" readonly />
                    <label for="experience">Experience</label>
                    <input type="text" name="experience" readonly />
                    <?php if ($data['show_permits']) : ?>
                    <label for="permits">Permit costs</label>
                    <input type="text" name="permits" readonly />
                    <?php else : ?>
                    <label for="seeds">Seeds</label>
                    <input type="text" name="seeds" readonly />
                    <?php endif; ?>
                    <label for="workforce">Select workers (max)</label>
                    <div class="me-auto">
                        <input name="workforce_amount" id="workforce_amount"
                            type="number" min="0" required />
                        <span id="data_container_avail_workforce">
                            ( {{ $workforce_data['avail_workforce'] }} )
                        </span>
                    </div>
                </form>
                @component('components.button', [
                    'id' => 'do-action',
                    'text' => $do_action_text,
                ])
                @endcomponent
            </div>
        </div>
    </div>
</div>
