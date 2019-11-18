<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name;?>.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS');?>select.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . '/header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="action_div">
                <div id="actions">
                    <div id="growing">
                        <p id="time"></p></br>
                    </div>
                    <button onmousedown="destroyCrops();"> Destroy crops </button>
                </div>
                <div id="select">
                    <?php
                    foreach($this->data['crop_types'] as $key): ?>
                    <img class="crop" src="<?php echo constant('ROUTE_IMG') . $key['crop_type'] . '.png';?>"
                    alt="<?php echo $key['crop_type'];?>"/>
                    <?php endforeach;?>
                </div>
                <div id="data_container">
                    <div id="data">
                        <form id="data_form">
                            <figure></figure>
                            <div class="row">
                                <label for="crop"> Crop: </label>
                                <input type="text" name="crop" readonly /></br>
                            </div>
                            <label for="time"> Time: </label>
                            <input type="text" name="time" readonly /></br>
                            <label for="seeds"> Seeds: </label>
                            <input type="text" name="seeds" readonly /></br>
                            <label for="workforce"> Workforce:</label>
                            <input name="workforce" id="" type="number" min="0" required />
                            <span>(<?php echo $this->data['workforce_data']['avail_workforce']?>)</span>
                            <button type="button"> Grow </button>
                        </form>
                    </div>
                </div>
            </div>
            <div id="crops_action">
                <div id="growing">
                    <p id="time"></p></br>
                </div>
                Actions: 
                <button onmousedown="destroyCrops();"> Destroy crops </button>
                Plant:
                <form id="plant">
                    <label for="type"> Select crop type:</label>
                    <select name="type" id="form_select" onchange="img();" required>
                        <option></option>
                        <?php
                            $crop_types = array_column($this->data['crop_types'], 'crop_type');
                            foreach($crop_types as $key): ?>
                                <option value="<?php echo $key;?>"><?php echo ucfirst($key);?></option>
                            <?php endforeach;?>
                    </select><img src="#" id="type_img" height="50px" width="50px"/></br>
                    <label for="quantitiy"> Select amount of fields: </label>
                    <input name="quantity" id="crop_quantity" type="number" min="0" required />
                    <span>(<?php echo $this->data['fields']['fields_avail']; ?>)</span></br>
                    <label for="workfore"> Select amount of workers:</label>
                    <input name="workforce" id="crop_workforce" type="number" min="0" required />
                    (<?php echo $this->data['workforce_data']['avail_workforce'];?>)</br>
                    <!---<label for="estimated"> Estimated time:</label>
                    <input name="estimated" id="plant_estimated" type="text" min="0" />-->
                    <button type="button" id="plant_button"> Grow </button>
                </form>
            </div>
            
        <div id="inventory">
            <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
        </div>
                    <div id="seed_g">
                <p>Select a item to get seeds from:</p>
                <div id="selected">
                    <div id="selected_t"></div>
                </div>
                <input type="number" id="amount" min="0" />
                <button> Generate </button>
            </div>
        <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        <script src="<?php echo constant('ROUTE_JS') . 'selectitem.js';?>"></script>
        <script src="<?php echo constant('ROUTE_JS') . 'select.js';?>"></script>  
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>            
</html>