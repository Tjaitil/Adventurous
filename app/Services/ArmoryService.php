<?php

namespace App\Services;

use App\Dto\ItemDto;
use App\Enums\ArmoryParts;
use App\Models\ArmoryItemsData;
use App\Models\Soldier;
use App\Models\SoldierArmory;
use App\Models\UserData;

final class ArmoryService
{
    public function __construct(
        protected SkillsService $skillsService,
        protected WarriorService $warriorService,
    ) {}

    public function isValidArmoryItem(string $item): false|ArmoryItemsData
    {
        $ArmoryItem = ArmoryItemsData::where('item', $item)->first();

        if ($ArmoryItem instanceof ArmoryItemsData) {
            return $ArmoryItem;
        } else {
            return false;
        }

    }

    /**
     * Change warrior part and add removing items to invenory_items
     *
     * @return array<int, ItemDto>
     */
    public function changeSoldierArmory(
        bool $is_removing,
        string $item,
        int $amount,
        ?string $hand,
        ArmoryParts $part,
        SoldierArmory $SoldierArmory,
    ): array {

        /**
         * @var array<int, ItemDto>
         */
        $removedItems = [];

        // check part
        switch ($part) {
            case ArmoryParts::AMMUNITION:

                if ($is_removing) {
                    if ($SoldierArmory->ammunition !== null) {
                        $removedItems[] = new ItemDto($SoldierArmory->ammunition, $SoldierArmory->ammunition_amount);
                    }
                    $SoldierArmory->ammunition = null;
                    $SoldierArmory->ammunition_amount = 0;
                } elseif ($item === $SoldierArmory->ammunition) {
                    $SoldierArmory->ammunition_amount += $amount;
                } else {
                    if ($SoldierArmory->ammunition !== null) {
                        $removedItems[] = new ItemDto($SoldierArmory->ammunition, $SoldierArmory->ammunition_amount);
                    }
                    $SoldierArmory->ammunition = $item;
                    $SoldierArmory->ammunition_amount = $amount;
                }

                // Remove right and left hand if throwing weapon is present
                if (strpos($item, 'throwing') && ! $is_removing) {
                    if ($SoldierArmory->right_hand !== null) {
                        $removedItems[] = new ItemDto($SoldierArmory->right_hand, 1);
                    }
                    if ($SoldierArmory->left_hand) {
                        $removedItems[] = new ItemDto($SoldierArmory->left_hand, 1);
                    }

                    $SoldierArmory->right_hand = null;
                    $SoldierArmory->left_hand = null;
                }

                break;
            case ArmoryParts::RIGHT_HAND:
                if ($SoldierArmory->right_hand !== null) {
                    $removedItems[] = new ItemDto($SoldierArmory->right_hand, 1);
                }

                if ($is_removing) {
                    $SoldierArmory->right_hand = null;
                } else {
                    $SoldierArmory->right_hand = $item;
                }
                break;
            case ArmoryParts::LEFT_HAND:
                if ($SoldierArmory->left_hand !== null) {
                    $removedItems[] = new ItemDto($SoldierArmory->left_hand, 1);
                }

                if ($is_removing) {
                    $SoldierArmory->left_hand = null;
                } else {
                    $SoldierArmory->left_hand = $item;
                }

                if (strpos($item, 'shield') && ! $is_removing) {
                    if ($SoldierArmory->right_hand !== null) {
                        $removedItems[] = new ItemDto($SoldierArmory->right_hand, 1);
                    }

                    $SoldierArmory->right_hand = null;
                }

                break;
            case ArmoryParts::HAND:
                if ($hand === 'left_hand') {
                    if ($SoldierArmory->left_hand !== null) {
                        $removedItems[] = new ItemDto($SoldierArmory->left_hand, 1);
                    }

                    if ($is_removing) {
                        $SoldierArmory->left_hand = null;
                    } else {
                        $SoldierArmory->left_hand = $item;
                    }
                } else {

                    if ($SoldierArmory->right_hand !== null) {
                        $removedItems[] = new ItemDto($SoldierArmory->right_hand, 1);
                    }

                    if ($is_removing) {
                        $SoldierArmory->right_hand = null;
                    } else {
                        if (strpos($item, 'bow')) {
                            if ($SoldierArmory->left_hand !== null) {
                                $removedItems[] = new ItemDto($SoldierArmory->left_hand, 1);
                            }

                            $SoldierArmory->left_hand = null;
                        }

                        $SoldierArmory->right_hand = $item;
                    }
                }

                break;
            case ArmoryParts::BODY:
                if ($SoldierArmory->body !== null) {
                    $removedItems[] = new ItemDto($SoldierArmory->body, 1);
                }

                if ($is_removing) {
                    $SoldierArmory->body = null;
                } else {
                    $SoldierArmory->body = $item;
                }
                break;
            case ArmoryParts::HELM:
                if ($SoldierArmory->helm !== null) {
                    $removedItems[] = new ItemDto($SoldierArmory->helm, 1);
                }

                if ($is_removing) {
                    $SoldierArmory->helm = null;
                } else {
                    $SoldierArmory->helm = $item;
                }
                break;

            case ArmoryParts::LEGS:

                if ($SoldierArmory->legs !== null) {
                    $removedItems[] = new ItemDto($SoldierArmory->legs, 1);
                }

                if ($is_removing) {
                    $SoldierArmory->legs = null;
                } else {
                    $SoldierArmory->legs = $item;
                }
                break;
            case ArmoryParts::BOOTS:
                if ($SoldierArmory->boots !== null) {
                    $removedItems[] = new ItemDto($SoldierArmory->boots, 1);
                }

                if ($is_removing) {
                    $SoldierArmory->boots = null;
                } else {
                    $SoldierArmory->boots = $item;
                }
                break;

            default:
                break;
        }

        return $removedItems;
    }

    /**
     * Check if warrior can equip item
     */
    public function hasCorrectSoldierTypeForItem(Soldier $Soldier, ArmoryItemsData $item_data): bool
    {
        if ($Soldier->type === $item_data->warrior_type || $item_data->warrior_type === 'all') {
            return true;
        }

        return false;
    }

    public function isItemUnlocked(ArmoryItemsData $ArmoryItem, UserData $Player): bool
    {
        if (\strpos($ArmoryItem->item, 'wujkin') !== false) {
            return $Player->isWujkinItemUnlocked();
        } elseif (\strpos($ArmoryItem->item, 'frajrite') !== false) {
            return $Player->isFrajriteItemUnlocked();
        }

        return true;
    }
}
