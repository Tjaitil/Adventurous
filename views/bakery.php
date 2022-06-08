bakery.css|bakery.js|
<h1 class="page_title"> Bakery </h1>
<div id="bakery">
    <div class="help">
        <p>Here you can make food to decrease your hunger. Players with farmer profiency pay 75 % less
            </br> For more information head to <a href="gameguide/bakery" target="_blank">gameguide/bakery</a></p>
    </div>
    <?php get_template("storeContainer", array("container_items" => $this->data), true, false, array("site" => "bakery"));?>

