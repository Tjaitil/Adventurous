<?php

namespace Tests\Utils\Traits;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;

trait EventAssertions
{
    /**
     * @param  value-of<\App\Enums\GameEvents>  $event
     */
    public function assertResponseHasEvent(TestResponse $response, string $event): void
    {
        $events = $response->json()['events'];
        if (! is_array($events)) {
            Assert::fail('No level up messages found in response');
        }

        $levelUpMessage = current(array_filter($events, (fn ($message) => $message === $event)));

        Assert::assertNotNull($levelUpMessage);
    }

    /**
     * @param  value-of<\App\Enums\GameEvents>  $event
     */
    public function assertResponseNotHasEvent(TestResponse $response, string $event): void
    {
        $events = $response->json()['events'] ?? [];
        if (! is_array($events)) {
            Assert::fail('No level up messages found in response');
        }

        $levelUpMessage = current(array_filter($events, (fn ($message) => $message === $event)));
        Assert::assertFalse($levelUpMessage);
    }
}
