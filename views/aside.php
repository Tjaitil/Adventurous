    <!--   
    <div id="side_menu">
        <h5> Categories</h5>
        <ul>
            <li><a href="#">Rules</a></li>
            <li><a href="#">Forum</a></li>
            <li><a href="#">Latest patch</a></li>
            <li><a href="/gameguide">Gameguide</a></li>
            <li><a href="#">FAQ & help</a></li>
        </ul>
    </div>
    <div id="side_menu2">
        
        
    </div>
    -->
    <div id="sidebar">
        <button onclick="sidebar.toggleSidebar();" id="sidebar_button_toggle"> << </button>
        <button class="sidebar_button">Adventure</button>
        <button class="sidebar_button">Countdowns</button>
        <button class="sidebar_button">Diplomacy</button>
        <div class="sidebar_tab" id="tab_1">
            1
        </div>
        <div class="sidebar_tab" id="tab_2">
            <table>
                <caption> Countdowns </caption>
                <thead>
                    <tr>
                        <td> Profiency </td>
                        <td> What </td>
                    </tr>
                </thead>
                <tr>
                    <td> Farmer </td>
                    <td><p>Towhar: <?php echo $this->data['countdowns']['farmer'][0];?></p>
                        <p>Cruendo: <?php echo $this->data['countdowns']['farmer'][1];?></p>
                    </td>
                </tr>
                <tr>
                    <td> Miner </td>
                    <td><p>Golbak: <?php echo $this->data['countdowns']['miner'][0];?></p>
                        <p>Snerpiir: <?php echo $this->data['countdowns']['miner'][1];?></p>
                    </td>
                </tr>
                <tr>
                    <td> Trader </td>
                    <td><?php echo 'Assignment: ' , $this->data['countdowns']['trader'];?></td>
                </tr>
                <tr>
                    <td> Warrior </td>
                    <td><p><?php echo 'Armymission: ' , $this->data['countdowns']['warrior']['mission'];?></p>
                        <p><?php echo 'Warrior(s) finished: ' , $this->data['countdowns']['warrior']['finished'];?></p>
                        <p><?php echo 'Warrior(s) training: ' , $this->data['countdowns']['warrior']['training'];?></p>
                        <p><?php echo 'Warrior(s) idle: ' , $this->data['countdowns']['warrior']['idle'];?></p>
                </tr>
            </table>
        </div>
        <div class="sidebar_tab" id="tab_3">
            3
        </div>
    </div>
