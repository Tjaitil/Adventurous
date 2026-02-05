<?php

namespace Tests\Feature\Controllers;

use Tests\ConversationTestCase;

final class ConversationControllerTest extends ConversationTestCase
{
    public function test_starting_conversation_returns_success(): void
    {
        $response = $this->post('/conversation/next', [
            'is_starting' => true,
            'person' => 'kapys',
            'selected_option' => 0,
        ]);

        $response->assertJsonStructure([
            'conversation_segment',
        ])->assertStatus(200);
    }

    public function test_continuing_conversation_returns_success(): void
    {
        // First, start the conversation
        $this->post('/conversation/next', [
            'is_starting' => true,
            'person' => 'kapys',
            'selected_option' => 0,
        ]);

        // Then continue it
        $response = $this->post('/conversation/next', [
            'is_starting' => false,
            'person' => 'kapys',
            'selected_option' => 0,
        ]);

        $response->assertJsonStructure([
            'conversation_segment',
        ])->assertStatus(200);
    }

    public function test_starting_conversation_with_nonexistent_person_returns_error(): void
    {
        $response = $this->post('/conversation/next', [
            'is_starting' => true,
            'person' => 'non-existing-person',
            'selected_option' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_continuing_conversation_with_nonexistent_person_returns_error(): void
    {
        // First, start a valid conversation
        $this->post('/conversation/next', [
            'is_starting' => true,
            'person' => 'kapys',
            'selected_option' => 0,
        ]);

        // Then try to continue with a non-existent person
        $response = $this->post('/conversation/next', [
            'is_starting' => false,
            'person' => 'non-existing-person',
            'selected_option' => 0,
        ]);

        $response->assertStatus(422);
    }
}
