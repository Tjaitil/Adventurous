        <div id="nav">
            <div class="top_bar">
                <div class="top_a"><a href="/main"></a></div>
                <div class="top_but"><a href="/main">Main</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/advclient"></a></div>
                <div class="top_but"><a href="advclient">Client</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/gameguide"></a></div>
                <div class="top_but"><a href="/gameguide">Gameguide</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/highscores"></a></div>
                <div class="top_but"><a href="/highscores">Highscores</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/news"></a></div>
                <div class="top_but"><a href="/news">News</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/profile"></a></div>
                <div class="top_but"><a href="/profile">Profile</a></div>
            </div>
            <div class="top_bar">
                <form action="/logout" method="POST">
                    @csrf
                    <div class="top_a">
                        <x-baseLinkButton type="submit" />
                    </div>
                    <div class="top_but"><x-baseLinkButton class="text-white" type="submit">Log out</x-baseLinkButton></div>
                </form>
            </div>
        </div>