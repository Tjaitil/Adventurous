        <?php
            require_once(constant('ROUTE_HELPER') . 'urlcheck.php');
            urlcheck();
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8"/>
        <link rel='shortcut icon' type='image/x-icon' href='<?php echo constant('ROUTE_IMG') . 'favicon.ico';?>' />
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Old+Standard+TT&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Martel&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>header.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>layout.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>aside.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>selectContainer.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>progressbar.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>warriorSelect.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>battleresult.css" />
        <?php if(strpos($_SERVER['REQUEST_URI'], 'advclient')): ?>
            <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>map.css" />
        <?php endif;?>
        