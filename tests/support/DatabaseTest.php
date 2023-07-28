<?php

namespace App\tests\support;

use App\libs\database;

trait DatabaseTest
{
    /**
     * 
     * @return void 
     */
    public function startTransaction()
    {
        database::getInstance()->beginTransaction();
    }



    /**
     * 
     * @return void 
     * @throws \Throwable 
     */
    public function rollbackTransaction()
    {
        database::getInstance()->rollBack();
    }
}
