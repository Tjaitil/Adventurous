<div id="map_container"
    class="max-h-3/4 b border-ridge absolute top-0 z-50 h-[75vh] max-h-[75vh] w-11/12 rounded-lg border-8 border-r border-primary-800 transition-all duration-200 invisible">
    <div id="map_container_header" class=" flex m-0 bg-orange-50 py-2 px-1">
        <img id="toggle_icon_list_image" class="cur-pointer"
            src="{{ asset('images/symbol icon.png') }} ">
        <div class="relative">
            <div id="map_type_toggle_overlay" class="w-[48px] h-[48px] absolute z-10 rounded-md opacity-60 top-0 left-0 invisible cursor-pointer bg-cyan-700"></div>
            <img id="toggle_world_image" src="{{ 'images/globe.png' }}" class="max-w-none cursor-pointer w-[48px] h-[48px]" />
        </div>
        <h2 id="map_header" class="w-full text-3xl">Local map</h2>
        <img id="close_map_button" class="cont_exit" src="{{ asset('images/exit.png') }}"
            width="20px" height="20px" />
    </div>
    <div id="map_icon_list"
        class="w invisible absolute left-0 h-auto w-0 max-w-[180px] overflow-x-hidden bg-primary-500 text-left transition-all duration-200 z-50">
        <ul class="list-none p-4">
            <x-map.mapListIcon src="boat travel icon.png" text="Boat travel" />
            <x-map.mapListIcon src="pesr travel icon.png" text="Pesr travel" />
            <x-map.mapListIcon src="combat icon.png" text="Combat" />
        </ul>
    </div>
    <div id="map_local_img_container" class="relative h-full overflow-scroll">
        <img id="local_img" src="{{ asset('images/' . $map_location . 'm.png') }}"
            class="h-[1600px] w-[1600px] max-w-none" />

        <span id="map_player_marker" class="w-[24px] h-[24px] top-0 left-0 bg-red-500 rounded-xl absolute"></span>
    </div>
    <div id="map_world_img_container"
        class="relative hidden h-full grid-cols-[repeat(9,_200px)] overflow-scroll bg-primary-500">
        @php

            $y = 1;
            $x = 1;
            $array = ['1.1', '2.1', '3.1', '4.1', '5.1', '6.1', '7.1', '8.1', '9.1', '1.2', '9.2', '9.3', '1.4', '2.4', '9.4', '1.5', '8.5', '9.5', '1.6', '8.6', '9.6', '1.7', '2.7', '3.7', '7.7', '8.7', '9.7', '1.8', '2.8', '4.8', '5.8', '6.8', '7.8', '8.8', '9.8', '1.9', '5.9', '6.9', '7.9', '8.9', '4.10', '5.10', '6.10', '7.10', '8.10', '9.10'];
        @endphp
        @for ($i = 0; $i < 90; $i++)
            @php
                $src = $x . '.' . $y;
            @endphp
            @if (!in_array($src, $array))
                @php
                    $src = $x . '.' . $y . 'm';
                @endphp
            @else
            @endif
            <img class="world_img h-[200px] w-[200px] max-w-none image-auto-render" alt="map img" height="200px"
                src="{{ asset('images/' . $src . '.png') }}">
            @php

                $x++;
                if ($x == 10) {
                    $x = 1;
                    $y++;
                }
            @endphp
        @endfor
    </div>
</div>
