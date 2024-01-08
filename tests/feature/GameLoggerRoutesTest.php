<?php

namespace Tests\Feature;

use Tests\Support\UserTrait;
use Tests\TestCase;

class GameLoggerRoutesTest extends TestCase
{
    use UserTrait;

    public function test_new_game_log_is_added(): void
    {
        $log = [
            'text' => 'You are in the town of Towhar',
            'type' => 'info',
        ];

        $response = $this->actingAs($this->getRandomUser())
            ->post('/log', $log);

        $response->assertStatus(200);
    }
}
