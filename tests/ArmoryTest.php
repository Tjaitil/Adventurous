<?php

namespace App\tests;

use App\controllers\ArmoryController;
use App\libs\Request;

class ArmoryTest extends BaseTest
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test_add_bow()
    {
        $_SERVER['REQUEST_METHOD'] = "POST";
        $request = new Request();
        $request->setRequestData(
            [
                "warrior_id" => 1,
                "item" => "oak bow",
                "amount" => 20,
                "is_removing" => false,
                "part" => "",
                "hand" =>  ""
            ]
        );

        $this->setRequestAsDependency($request);
        return $this->callMethod(ArmoryController::class, "add");
    }


    public function test_unlockable_items()
    {
        $_SERVER['REQUEST_METHOD'] = "POST";
        $request = new Request();
        $request->setRequestData(
            [
                "warrior_id" => 1,
                "item" => "frajrite sword",
                "amount" => 1,
                "is_removing" => false,
                "part" => "",
                "hand" =>  "right"
            ]
        );

        $this->setRequestAsDependency($request);
        return $this->callMethod(ArmoryController::class, "add");
    }

    public function test_remove()
    {
        $_SERVER['REQUEST_METHOD'] = "POST";
        $request = new Request();
        $request->setRequestData(
            [
                "warrior_id" => 4,
                "item" => "adron throwing knives",
                "amount" => 10,
                "is_removing" => true,
                "part" => "ammunition",
                "hand" => ""
            ]
        );

        $this->setRequestAsDependency($request);
        return $this->callMethod(ArmoryController::class, "remove");
    }

    public function test_check_warrior_type()
    {
        $_SERVER['REQUEST_METHOD'] = "POST";
        $request = new Request();
        $request->setRequestData(
            [
                "warrior_id" => 4,
                "item" => "adron throwing knives",
                "amount" => 10,
                "is_removing" => false,
                "part" => "ammunition",
                "hand" => ""
            ]
        );

        $this->setRequestAsDependency($request);
        return $this->callMethod(ArmoryController::class, "remove");
    }
}
