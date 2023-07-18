<!DOCTYPE html>
<html>

<head>
    <title><?php echo ucwords($title); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS') . $name ?>.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS') ?>conversation.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant("ROUTE_CSS"); ?>news.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant("ROUTE_COMPILED"); ?>css/output.css" />
    <?php require(constant('ROUTE_VIEW') . 'head.php'); ?>
</head>

<body class="grid grid-cols-6">
    <header class="col-span-6 row-start-1">
        <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
    </header>
    <section class="col-span-5 col-start-2 row-start-2">
        <?php if (array_search($name, array('advclient', 'gameguide')) === false) : ?>
            <script src=" <?php echo constant('ROUTE_JS') . $name . '.js'; ?>">
            </script>
        <?php endif; ?>
        <?php require(constant('ROUTE_VIEW') . $name . '.php'); ?>
    </section>
    <?php if (array_search($name, array("gameguide", "profile", "error", "main", "highscores", "news", "messages")) === false) : ?>
        <aside class="col-span-1 col-start-1 row-start-2">
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    <?php endif; ?>
    <footer class="col-span-5 col-start-2 row-start-3">
        Delevoped by Kjetil Baksaas
    </footer>
</body>

</html>