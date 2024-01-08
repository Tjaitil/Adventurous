@props(['messages'])
<div id="log_container" class="w-full">
    <x-borderInterfaceContainer>
        <div id="log" class="darkTextColor relative m-0 h-52 overflow-y-scroll bg-orange-50">
            <table id="game_messages" class="mt-0 w-full p-2">
                <tbody>
                    @foreach ($messages as $message)
                        <tr>
                            <td class="log_message border-none bg-transparent p-[2px] text-left">
                                <p @class([
                                    'log_message my-0',
                                    'text-red-600' => $message['type'] === 'error',
                                    'text-yellow-600' => $message['type'] === 'warning',
                                    'text-green-600' => $message['type'] === 'success',
                                ])>
                                    {{ '[' . $message['timestamp'] . '] ' }}
                                    {{ $message['text'] }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-borderInterfaceContainer>
</div>
