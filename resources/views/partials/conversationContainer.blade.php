<div id="conversation-container"
    class="invisible absolute z-20 h-[170px] rounded border-4 border-primary-700 bg-orange-50 p-1 shadow-lg transition-[scale] duration-500 ease-in after:pointer-events-none after:absolute after:left-[-1px] after:top-[-1px] after:h-[calc(100%+2px)] after:w-[calc(100%+2px)] after:rounded after:border-4 after:border-solid after:border-gray-950 after:content-['']">
    <img class="cont_exit" src="{{ asset('images/exit.png') }}" />
    <h3 id="conversation-header" class="border-b-2 border-primary-900 py-1 text-xl"></h3>
    <div class="h-22 flex justify-center overflow-hidden">
        <img src="#" id="conversation-a" class="block h-16 w-16" />
        <div id="conversation-text-wrapper" class="max-h-[110px] w-80 overflow-y-scroll py-2">
            <ul class="mb-1 list-none">
            </ul>
        </div>
        <img src="#" id="conversation-b" class="block h-16 w-16" />
    </div>
    <button id="conv_button">Click here to continue</button>
</div>
