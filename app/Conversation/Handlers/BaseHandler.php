<?php

namespace App\Conversation\Handlers;

use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;

abstract class BaseHandler
{
    public function __construct() {}

    /**
     * @var array<string, array<string, string>>
     */
    protected array $replacers = [];

    /**
     * @var array<string, string>
     */
    protected array $serverEvent = [];

    /**
     * @var array<string, string>
     */
    protected array $conditionals = [];

    /**
     * @var array<string, string>
     */
    protected array $clientCallBacks = [];

    /**
     * @var array<string, array<int, string>>
     */
    protected array $clientEvents = [];

    public function currentLocationConditional(string $location, #[CurrentUser] User $User): bool
    {
        return $User->player->location === $location;
    }

    public function getServerEvent(string $index): ?string
    {
        return $this->getCallable($index, 'serverEvent');
    }

    /**
     * @return array<int, string>|null
     */
    public function getClientEvent(string $index): ?array
    {
        $foo = $this->getCallable($index, 'clientEvents');

        return $foo;
    }

    public function createIndexWithOptionId(string $index, int $id): string
    {
        return $index.'#'.$id;
    }

    public function getClientCallBack(string $index): ?string
    {
        return $this->getCallable($index, 'clientCallBacks');
    }

    /**
     * @return array<string, string>|null
     */
    public function getReplacers(string $index): ?array
    {
        return $this->getCallable($index, 'replacers');
    }

    public function getConditional(string $index): ?string
    {
        return $this->getCallable($index, 'conditionals');
    }

    /**
     * @template T of string
     *
     * @param  T  $bag
     * @return ($bag is 'clientEvents' ? array<int, string>|null : ($bag is 'replacers' ? array<string, string>|null : string|null))
     */
    public function getCallable(string $index, string $bag): string|array|null
    {
        return match ($bag) {
            'replacers' => $this->replacers[$index] ?? null,
            'serverEvent' => $this->serverEvent[$index] ?? null,
            'clientEvents' => $this->clientEvents[$index] ?? null,
            'clientCallBacks' => $this->clientCallBacks[$index] ?? null,
            'conditionals' => $this->conditionals[$index] ?? null,
            default => throw new Exception('Invalid bag type: '.$bag),
        };
    }
}
