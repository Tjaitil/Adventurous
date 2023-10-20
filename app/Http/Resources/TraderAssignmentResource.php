<?php

namespace App\resources;

/**
 * @property int $assignment_id Trader assignment id
 * @property int $cart_amount Amount of items in cart
 * @property int $cart_type Type of cart the user has
 * @property int $delivered Amount the user has delivered
 * @property string $trading_countdown Countdown for the assignment
 * @property int $cart_capasity Countdown for the assignment
 * @property int $base
 * @property int $destination
 * @property int $cargo
 * @property int $assignment_amount
 * @property int $assignment_type
 * @property int $assignment_reward
 * @property int $assignment_time
 */
class TraderAssignmentResource extends Resource
{

    public function __construct($resource = null)
    {
        parent::__construct([
            "assignment_id" => "",
            "cart_amount" => "",
            "cart_type" => "",
            "delivered" => "",
            "trading_countdown" => "",
            "cart_capasity" => "",
            "base" => "",
            "destination" => "",
            "cargo" => "",
            "assignment_amount" => "",
            "assignment_type" => "",
            "assignment_reward" => "",
            "assignment_time" => ""
        ], $resource);
    }

    public function toResource()
    {
        return $this;
    }

    /**
     * Convert resource to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            "assignment_id" => $this->assignment_id,
            "cart_amount" => $this->cart_amount,
            "cart_type" => $this->cart_type,
            "delivered" => $this->delivered,
            "trading_countdown" => $this->trading_countdown,
            "cart_capasity" => $this->cart_capasity,
            "base" => $this->base,
            "destination" => $this->destination,
            "cargo" => $this->cargo,
            "assignment_amount" => $this->assignment_amount,
            "assignment_type" => $this->assignment_type,
            "assignment_reward" => $this->assignment_reward,
            "assignment_time" => $this->assignment_time
        ];
    }

    public static function mapping(array $data): array
    {

        if (!isset($data['assignment_id'])) {
            $data['assignment_id'] = -1;
        }
        if (isset($data['time'])) {
            $data['assignment_time'] = intval($data['time']);
        }

        return $data;
    }
}
