
<?php
// router.php
$path = pathinfo($_SERVER["SCRIPT_FILENAME"]);
if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
} else if ($path["extension"] == "el") {
    header("Content-Type: text/x-script.elisp");
    readfile($_SERVER["SCRIPT_FILENAME"]);
} else {
    require(__DIR__ . "/index.php");
}

?>
