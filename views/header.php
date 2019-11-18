        <div id="nav">
            <div id="nav_2">
                <button id="nav_but" onclick="displayNav();"> Click </button>
                <ul>
                    <li><a href="#">Rules</a></li>
                    <li><a href="#">Forum</a></li>
                    <li><a href="#">Latest patch</a></li>
                    <li><a href="/gameguide">Gameguide</a></li>
                    <li><a href="#">FAQ & help</a></li>
                </ul>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/main">1</a></div>
                <div class="top_but"><a href="/main">Main</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/city">2</a></div>
                <div class="top_but"><a href="/city">City</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/travel">3</a></div>            
                <div class="top_but"><a href="/travel">Travel</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/adventures">4</a></div>
                <div class="top_but"><a href="/adventures">Adventures</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/highscores">5</a></div>
                <div class="top_but"><a href="/highscores">Highscores</a></div>
            </div>
            <div class="top_bar">
                <div class="top_a"><a href="/messages">6</a></div>
                <div class="top_but"><a href="/messages">Messages</a></div>
            </div>
        </div>
        <a href="/profile"> Profile </a>
        <a href="/logout" id="logout"> Log Out</a>
        <div id="clock"> 00:00:00 </div>
        <script src="<?php echo '../' . constant('ROUTE_JS') . 'general.js';?>"></script>
