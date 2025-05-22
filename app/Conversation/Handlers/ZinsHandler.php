<?php

namespace App\Conversation\Handlers;

final class ZinsHandler extends BaseHandler
{
    /**
     * @var array<string, string>
     */
    protected array $clientCallBacks = [
        'zrrrr1#0' => 'LoadZinsStoreCallback',
    ];
}
