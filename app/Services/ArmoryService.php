<?php

namespace App\services;

use App\models\ArmoryItemsData;
use App\models\WarriorsArmory;

class ArmoryService
{
    private $inventory_items = [];
    private $warrior = null;
    private bool $is_removing;
    private string $item;
    private string $type;

    public function __construct()
    {
    }

    /**
     * Change warrior part and add removing items to invenory_items
     * @param bool $is_removing
     * @param string $item
     * @param int $amount
     * @param string $hand
     * @param ArmoryItemsData $item_data
     * @param WarriorsArmory $warrior
     * 
     * @return Warrior|bool
     */
    public function changeWarriorPart(
        bool $is_removing,
        string $item,
        int $amount,
        string $hand,
        ArmoryItemsData $item_data,
        WarriorsArmory $warrior,
    ) {
        $this->warrior = $warrior;
        $this->is_removing = $is_removing;
        $this->item = $item;
        $this->type = $item_data['type'];

        // check part
        switch ($item_data['type']) {
            case 'ammunition':
                $old_item = $warrior->ammunition;
                $old_ammunition_amount = $warrior->ammunition_amount;

                if ($old_item !== $item) {
                    $this->addInventoryItem($old_item, $old_ammunition_amount);
                } else {
                    $amount = $amount + $warrior->ammunition_amount;
                }

                // Remove right and left hand if throwing weapon is present
                if (strpos($item, "throwing") && !$is_removing) {
                    $this->addInventoryItem($warrior->right_hand);
                    $this->addInventoryItem($warrior->left_hand);

                    $warrior->right_hand = "none";
                    $warrior->left_hand = "none";
                }

                if ($is_removing) {
                    $warrior->ammunition = "";
                    $warrior->ammunition_amount = 0;
                } else {
                    $warrior->ammunition = $item;
                    $warrior->ammunition_amount = $amount;
                }

                break;
            case 'left_hand':
            case 'right_hand':
                if ($hand === 'left_hand') {
                    $this->checkIfRemoving();
                } else {
                    $this->checkIfRemoving();

                    if (strpos($item, 'bow') && !$is_removing) {
                        $this->addInventoryItem($warrior->left_hand);

                        $warrior->left_hand = "none";
                    }
                }

                break;
            case 'helm':
            case 'body':
            case 'boots':
                $this->checkIfRemoving();
                break;

            default:
                return false;
                break;
        }
        return $this->warrior;
    }
    /**
     * Check if items is removing and add to inventory items else assign item to warrior
     *
     * @return void
     */
    private function checkIfRemoving()
    {
        if ($this->is_removing) {
            $this->addInventoryItem($this->warrior->{$this->type});
            $this->warrior->{$this->type} = "none";
        } else {
            $this->warrior->{$this->type} = $this->item;
        }
    }

    /**
     * Check if warrior can equip item
     *
     * @param WarriorsArmory $warrior
     * @param ArmoryItemsData $item_data
     *
     * @return bool
     */
    public function hasCorrectWarriorTypeForItem(WarriorsArmory $warrior, ArmoryItemsData $item_data)
    {
        if ($warrior->warrior->type === $item_data->warrior_type || $item_data->warrior_type === 'all') {
            return true;
        }

        return false;
    }

    /**
     * Add inventory items to be updated
     *
     * @param string $item Item name
     * @param int $amount Item amount
     *
     * @return void
     */
    private function addInventoryItem(string $item, int $amount = 1)
    {
        if ($item === "none") return;

        $this->inventory_items[] = [
            "item" => $item,
            "amount" => $amount
        ];
    }

    public function getInventoryItemsToUpdate(): array
    {
        return $this->inventory_items;
    }
}
