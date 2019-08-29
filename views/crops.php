<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name?>.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . '/header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <?php /*if(strlen($_SESSION['gamedata']['log'][] ) > 3):?>
                <script>getgMessage();</script>
            <?php endif;*/?>
            <p id=""></p></br>
            <p id=""><?php echo (isset($this->data['notification'])) ? $this->data['notification'] : ""; ?></p>
            <p id="demo"></p>
            <!--<div id="crops_view">
                Currently growing: 
                <div id="toggle_view">
                    <a href="#" onclick="showFig();" id="crop_farm">Show farm</a>
                    <a href="#" onclick="showTable();" id="crop_list"> Show list</a></br> 
                </div>
                <table id="crops_table">
                        <thead>
                            <tr>
                                <th> Type of Crop</th>
                                <th> Number of fields </th>
                            </tr>
                                <td></td>
                                <td></td>
                            <tr>
                                <td>    1   </td>
                                <td>    2   </td>
                            </tr>
                        </thead>
                </table>
                <div id="crops">
                </div>
            </div>-->

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
                    (<?php echo $this->data['fields']['fields_avail']; ?>)</br>
                    <label for="workfore"> Select amount of workers:</label>
                    <input name="workforce" id="crop_workforce" type="number" min="0" required />
                    (<?php echo $this->data['workforce_data']['avail_workforce'];?>)</br>
                    <!---<label for="estimated"> Estimated time:</label>
                    <input name="estimated" id="plant_estimated" type="text" min="0" />-->
                    <button type="button" id="plant_button"> Grow </button>
                </form>
            </div>
        <script>
            /* var corn1 = 3;
            for (var i = 0; i < corn1; i++) {
                document.getElementById("crop_" + [i]).style.backgroundColor = "red";
            }
            function showTable () {
                document.getElementById("crops_table").style.visibility = "visible";
                document.getElementById("crops_table").style.display = "block";
                document.getElementById("crops").style.visibility = "hidden";
            }
            function showFig () {
                document.getElementById("crops_table").style.visibility = "hidden";
                document.getElementById("crops_table").style.display = "none";
                document.getElementById("crops").style.visibility = "visible";
            }
            */
            
        </script>
        <div id="inventory">
            <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
        </div>
                    <div id="seed_g">
                <p>Select a item to get seeds from:</p>
                <div id="selected">
                    <div id="selected_t"></div>
                </div>
                <input type="number" id="quantity" min="0" />
                <button> Generate </button>
            </div>
        <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        <script src="<?php echo constant('ROUTE_JS') . 'selectitem.js';?>"></script>  
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>            
</html>