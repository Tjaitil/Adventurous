<?php

/**
 * Simple wrapper to keep phpstan happy while removing outdated base controller class.
 * @deprecated Outdated base controller class
 */
class controller extends \App\libs\controller
{
    public function __construct()
    {
        parent::__construct();
    }
}
