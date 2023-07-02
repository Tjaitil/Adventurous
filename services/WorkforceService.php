<?php

class WorkforceService
{

    public function upgradeEffiency()
    {
    }

    /**
     * Check if user has enough workers
     *
     * @param int $avail_workforce Available workforce
     * @param int $assigned_workforce Assigned workforce
     * 
     * @throws Exception
     * @return bool
     */
    public function hasEnoughWorkers(int $avail_workforce, int $assigned_workforce)
    {

        if (
            $avail_workforce < $assigned_workforce
        ) {
            throw new Exception("You don't have enough workers ready");
        } else {
            return true;
        }
    }
}
