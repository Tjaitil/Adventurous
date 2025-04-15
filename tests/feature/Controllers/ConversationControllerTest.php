<?php

namespace Tests\Feature\Controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ConversationControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->getRandomUser());
    }

    #[DataProvider('isStartingProvider')]
    public function test_index_method_returns_success(bool $isStarting)
    {
        $response = $this->post('/conversation/next', [
            'is_starting' => $isStarting,
            'person' => 'kapys',
            'selected_option' => 0,
        ]);

        $response->assertJsonStructure([
            'conversation_segment',
        ])->assertStatus(200);
    }

    #[DataProvider('isStartingProvider')]
    public function test_index_method_returns_error_when_person_doesnt_exists(bool $isStarting)
    {
        $response = $this->post('/conversation/next', [
            'is_starting' => $isStarting,
            'person' => 'non-existing-person',
            'selected_option' => 0,
        ]);

        $response->assertStatus(422);
    }

    public static function isStartingProvider(): array
    {
        return [
            'true' => [true],
            'false' => [false],
        ];
    }
}
