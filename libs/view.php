<?php
class view
{
    public $title;
    function __construct()
    {
    }
    public function render($name, $title)
    {
        $this->title = $title;
        require(constant('ROUTE_VIEW') . $name . '.php');
    }
}
