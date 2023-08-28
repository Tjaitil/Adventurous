<h1 class="page_title">{{ $title }}</h1>
<div id="stockpile">
    @component('components.help')
        Click on items to withdraw or insert items into the stockpile. <br>
        When selecting custom amount press enter to submit.
    @endcomponent
    <div id="stockpile-list">
        @component('components.stockpile.itemList')
        @endcomponent
    </div>
    @component('components.stockpile.stockpileMenu')
    @endcomponent
</div>
