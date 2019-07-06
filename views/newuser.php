<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $name ?>.css" />
    </head>
    <body>
        <section>
           <div id="choose">
            <?php var_dump($this->error);
            var_dump($_SESSION);?>
            Welcome to the game! </br>
            To get started you must choose a profiency </br>
            <form method="post" action="/newuser">
                <label for="profiency"> Select your profiency! </label>
                <select name="profiency">
                    <option></option>
                    <option value="Farmer"> Farmer </option>
                    <option value="Miner"> Miner </option>
                    <option value="Warrior"> Warrior </option>
                </select><span class="choose_error"><?php echo $this->error['profiencyErr']?></span></br>
                <button type="submit"> Select! </button>
            </form></br>
            <a href="/gameguide" target="_blank"> Need more information? Click here</a>
           </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
    </body>
</html>
