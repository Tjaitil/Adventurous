        |zinsstore.js|
        <h1 class="page_title"> Zins Store </h1>
        <p class="help">
            Trade loot from daqloons. The list contains item Zins is willing to trade. <br>
            Select a trade from the container below
        </p>
            <?php
                $data = array();
                $data['container_items'] = array();
                $data['container_items'][] = array("item" => "daqloon horns", "price" => 1500);
                $data['container_items'][] = array("item" => "daqloon scale", "price" => 25);
            ?>

            <?php get_template("storeContainer", $data, true, false, false);?>
        </div>