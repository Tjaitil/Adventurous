        <div id="nav">
            <div id="nav_2">
                <ul id="nav_2">
                    <li><a href="/main">Main</a></li>
                    <li><a href="/city">City</a></li>
                    <li><a href="/travel">Travel</a></li>
                    <li><a href="/adventures">Adventures </a></li>
                    <li><a href="/highscores">Highscores </a></li>
                    <li><a href="/messages">Messages </a></li>
                    <!--<li><div class="but"><a href="/main">Main</a></div></li>
                    <li><div class="but"><a href='/city'>City</a></div></li>
                    <li><div class="but"><a href="/travel">Travel</a></div></li>
                    <li><div class="but"><a href="/adventures">Adventures</a></div></li>
                    <li><div class="but"><a href="/highscores">Highscores</a></div></li>
                    <li><div class="but"><a href="/messages">Messages</a></div></li>-->
                </ul>
                <button id="nav_but" onclick="displayNav();"> Click </button>
            </div>
            <div class="top_bar">
                <div class="a"><a href="/main">1</a></div>
                <div class="but"><a href="/main">Main</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href='/city'>2</a></div>
                <div class="but"><a href='/city'>City</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="/travel">3</a></div>            
                <div class="but"><a href="/travel">Travel</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="/adventures">4</a></div>
                <div class="but"><a href="/adventures">Adventures</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="/highscores">5</a></div>
                <div class="but"><a href="/highscores">Highscores</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="/messages">6</a></div>
                <div class="but"><a href="/messages">Messages</a></div>
            </div>
        </div>
        <a href="/profile"> Profile </a>
        <a href="/logout" id="logout"> Log Out</a>
        <div id="clock"> 00:00:00 </div>
        <script src="<?php echo constant('ROUTE_JS') . 'general.js';?>"></script>
