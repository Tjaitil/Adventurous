            <h1 class="mb-1 mt-1" id="main-title"> Welcome to Adventurous <?php echo ucfirst($_SESSION['username']); ?></h1>
            <a href="/advclient" id="main-client-link">
                <div id="client-link-background">
                    Go To Client
                </div>
            </a>
            <div id="public_chat">
                <h3> Public chat</h3>
                <div id="chat">
                    <ul>

                    </ul>
                </div>
                <div id="chat_inputs">
                    <input type="text" id="text" placeholder="Write message" />
                    <button type="button" onclick="chat();"> Chat </button>
                </div>
            </div>
            <div id="main_wrapper">
                <div id="game_news">
                    <div id="news_header">
                        <h2> News </h>
                    </div>
                    <div id="news_container">
                        <?php
                        $arr = array();
                        $arr[] = array("news_type" => "game update", "news_title" => "Alpha Test", "news_introduction"  =>
                        "Alpha test is soon underway. Players who want to participate...");
                        for ($i = 0; $i < count($arr); $i++) : ?>
                            <div class="news_element">
                                <img src="<?php echo constant('ROUTE_IMG') . $arr[$i]['news_type']; ?>" alt="News picture" />
                                <h4 class="news_title"><?php echo $arr[$i]['news_title']; ?></h4>
                                <p class="news_introduction">
                                    <?php
                                    if (strlen($arr[$i]['news_introduction']) > 40) {
                                        $arr[$i]['news_introduction'] = substr($arr[$i]['news_introduction'], 0, 40);
                                        $arr[$i]['news_introduction'] .= '...';
                                    }
                                    echo $arr[$i]['news_introduction']; ?><a href="#" class="linkButton"> read more</a></p>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <p><a href="/news">Click here for more news</a></p>
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
                            <td>
                                <p>Towhar: <?php echo $this->data['countdowns']['farmer'][0]; ?></p>
                                <p>Cruendo: <?php echo $this->data['countdowns']['farmer'][1]; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td> Miner </td>
                            <td>
                                <p>Golbak: <?php echo $this->data['countdowns']['miner'][0]; ?></p>
                                <p>Snerpiir: <?php echo $this->data['countdowns']['miner'][1]; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td> Trader </td>
                            <td><?php echo 'Assignment: ', $this->data['countdowns']['trader']; ?></td>
                        </tr>
                        <tr>
                            <td> Warrior </td>
                            <td>
                                <p><?php echo 'Armymission: ', $this->data['countdowns']['warrior']['mission']; ?></p>
                                <p><?php echo 'Warrior(s) finished: ', $this->data['countdowns']['warrior']['finished']; ?></p>
                                <p><?php echo 'Warrior(s) training: ', $this->data['countdowns']['warrior']['training']; ?></p>
                                <p><?php echo 'Warrior(s) idle: ', $this->data['countdowns']['warrior']['idle']; ?></p>
                        </tr>
                    </table>
                </div>
            </div>