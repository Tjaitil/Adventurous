<?php

namespace Tests\Utils\Traits;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;

trait ExperienceAssertions
{
    public function assertResponseHasLevelUpMessage(TestResponse $response, string $skill, int $targetLevel): void
    {
        $levelUpMessages = $response->json()['levelUP'];
        if (! is_array($levelUpMessages)) {
            Assert::fail('No level up messages found in response');
        }

        $levelUpMessage = current(array_filter($levelUpMessages, (fn ($message) => $message['skill'] === $skill)));

        Assert::assertNotNull($levelUpMessage);
        Assert::assertEquals($skill, $levelUpMessage['skill']);
        Assert::assertEquals($targetLevel, $levelUpMessage['new_level']);
    }
}
