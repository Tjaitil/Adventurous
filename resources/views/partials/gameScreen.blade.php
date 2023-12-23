<div id="game-screen">
    <div id="canvas-border"
        class="absolute z-[7] w-[700px] h-[400px] rounded border-4 border-primary-700 after:pointer-events-none after:absolute after:left-[-1px] after:top-[-1px] after:h-[calc(100%+2px)] after:w-[calc(100%+2px)] after:rounded after:border-4 after:border-solid after:border-gray-950 after:content-['']"
    ></div>
    <x-client.canvas id="game_canvas" />
    <x-client.canvas id="game_canvas2" />
    <x-client.canvas id="game_canvas3" />
    <x-client.canvas id="game_canvas4" />
    <x-client.canvas id="text_canvas" />
    <x-client.canvas id="hud_canvas" />
</div>