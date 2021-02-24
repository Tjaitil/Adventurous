            <h2 class="page_title"> News </h2>       
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
            
            <div class="news_container">
                <?php
                    $arr = array();
                    $arr[] = array("news_type" => "game update", "news_title" => "news test", "news_introduction"  =>
                                   "Alpha test is soon underway. Players who want to participate...");
                    for($i = 0; $i < count($arr); $i++): ?>
                    <div class="news_element">
                        <img src="<?php echo constant('ROUTE_IMG') . $arr[$i]['news_type'];?>" />
                        <h4 class="news_title"><?php echo ucwords([$i]['news_title']);?></h4>
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