            <div id="public_chat">
            <div id="chat">
                <ul>
                    <?php get_template('chat', $this->data['chat']);?>            
                </ul>
            </div>
            <input type="text" id="text" placeholder="Write message" />
            <button type="button" onclick="chat();"> Chat </button>
            </div>
            <img src="map.png" id="world_map" height="300px" width="500px"/>
            
            <p id="demo"></p>
            <?php if($_SESSION['gamedata']['profiency'] === "none"): ?>
                <script src="public/js/tutorial.js"></script>
            <?php endif;?>
            <div id="main_wrapper">
            <div id="game_news">
                <div id="news_header">
                    <h3> News </h3>
                </div>
                <div id="news_container">
                    <?php
                        $arr = array();
                        $arr[] = array("news_type" => "game update", "news_title" => "Alpha Test", "news_introduction"  =>
                                       "Alpha test is soon underway. Players who want to participate...");
                        for($i = 0; $i < count($arr); $i++): ?>
                        <div class="news_element">
                            <img src="<?php echo constant('ROUTE_IMG') . $arr[$i]['news_type'];?>" alt="News picture" />
                            <h4 class="news_title"><?php echo $arr[$i]['news_title'];?></h4>
                            <p class="news_introduction">
                            <?php
                             if(strlen($arr[$i]['news_introduction']) > 40) {
                                $arr[$i]['news_introduction'] = substr($arr[$i]['news_introduction'], 0, 40);
                                $arr[$i]['news_introduction'] .= '...';
                             }
                            echo $arr[$i]['news_introduction'];?><button>read more</button></p>
                        </div>
                    <?php endfor;?>
                </div>
                <p><a href="/news">Click here for more news</a></p>
            </div>
            <div id="town_map">
                <button> Go to client </button>
            </div>
            <div id="profile">
                <span id="profile_header"> Player Card</span></br>
                <img id="profile_picture" src="" height="50%" width="74%" /></br>
                <span id="profile_profiency"><?php echo ucfirst($_SESSION['gamedata']['profiency']) . ' level '
                                                . $_SESSION['gamedata']['profiency_level'];?></span>
                <div id="skill_bar">
                    
                    <div id="skill_bar2">
                        
                    </div>
                    <div id="skill_bar_progress"><span id="progress_value1"><?php echo $_SESSION['gamedata']['profiency_xp'];?></span>
                    &nbsp/&nbsp<span id="progress_value2"><?php echo $_SESSION['gamedata']['profiency_xp_nextlevel']; ?></span></div>
                </div></br>
                <a href="#"> View more profile details >></a>
                <p id="demo"></p>
            </div>
            <div id="countdowns">
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
        </div>