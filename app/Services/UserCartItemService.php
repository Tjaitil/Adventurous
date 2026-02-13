<?php

namespace App\Services;

use App\Models\UserCartItem;

class UserCartItemService
{
    public function addItem(int $userId, int $itemId, int $amount): UserCartItem
    {
        $cartItem = UserCartItem::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->first();

        if ($cartItem) {
            $cartItem->amount += $amount;
            $cartItem->save();

            return $cartItem;
        }

        return UserCartItem::create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'amount' => $amount,
        ]);
    }

    public function setAmount(int $userId, int $itemId, int $amount): UserCartItem
    {
        return UserCartItem::updateOrCreate(
            [
                'user_id' => $userId,
                'item_id' => $itemId,
            ],
            [
                'amount' => $amount,
            ]
        );
    }

    public function removeAmount(int $userId, int $itemId, int $amount): void
    {
        $cartItem = UserCartItem::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->first();

        if ($cartItem) {
            $newAmount = $cartItem->amount - $amount;

            if ($newAmount <= 0) {
                $cartItem->delete();
            } else {
                $cartItem->amount = $newAmount;
                $cartItem->save();
            }
        }
    }

    public function hasItem(int $userId, int $itemId): bool
    {
        return UserCartItem::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->exists();
    }

    public function clearCart(int $userId): void
    {
        UserCartItem::where('user_id', $userId)->delete();
    }

    public function removeItem(int $userId, int $itemId): void
    {
        UserCartItem::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->delete();
    }
}
