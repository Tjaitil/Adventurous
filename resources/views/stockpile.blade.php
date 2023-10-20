<h1 class="page_title">{{ $title }}</h1>
<div id="stockpile">
    <x-help>
        Click on items to withdraw or insert items into the stockpile. <br>
        When selecting custom amount press enter to submit.
    </x-help>
    <div id="stockpile-list">
        <x-stockpile.itemList :stockpile="$Stockpile" :max-amount="$max_amount" />
    </div>
    <x-stockpile.stockpileMenu />
</div>
