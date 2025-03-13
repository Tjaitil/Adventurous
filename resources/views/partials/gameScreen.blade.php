<div id="game-screen" class="relative">
    <div id="canvas-border"
        class="absolute z-7 top-[-6px] w-[700px] h-[400px] border-8 border-primary-700 after:pointer-events-none after:absolute after:left-[-1px] after:top-[-1px] after:h-[calc(100%+2px)] after:w-[calc(100%+2px)] after:border-4 after:border-solid after:border-gray-950/60 after:content-[''] pixelated-corners">
    </div>
    <x-client.canvas id="game_canvas" />
    <x-client.canvas id="game_canvas2" />
    <x-client.canvas id="game_canvas3" />
    <x-client.canvas id="game_canvas4" />
    <x-client.canvas id="text_canvas" />
    <x-client.canvas id="hud_canvas" />
</div>
