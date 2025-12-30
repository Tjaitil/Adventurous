<?php

/**
 * Simple wrapper to keep phpstan happy while removing outdated base model class.
 * @deprecated Outdated base model class
 */
class model extends \App\libs\model
{
    public function __construct($table = null)
    {
        parent::__construct($table);
    }
}
