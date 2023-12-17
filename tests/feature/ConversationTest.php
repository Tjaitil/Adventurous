<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\Support\UserTrait;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use UserTrait;

    public function test_start_conversation_has_correct_index(): void
    {
        $data = Storage::disk('gamedata')->json('conversations/pesr.json');
        $User = $this->getRandomUser();
        $response = $this->actingAs($User)
            ->get('/conversation/next?person=pesr&is_starting=true');

        $response->assertStatus(200);
        $json = $response->json();
        $index = session()->get('conversation_index');
        foreach ($json['conversation_segment'] as $key => $segment) {
            $this->assertArrayNotHasKey('server_event', $segment);
        }
        $this->assertArrayHasKey('conversation_segment', $json);
        $this->assertEquals($data['index'], $index);
    }

    public function test_continue_conversation_has_correct_index(): void
    {
        $data = Storage::disk('gamedata')->json('conversations/pesr.json');
        $User = $this->getRandomUser();

        session()->put('conversation_index', 'pr');

        $response = $this->actingAs($User)
            ->get('/conversation/next?person=pesr&nextKey=r');

        $response->assertStatus(200);
        $json = $response->json();
        foreach ($json['conversation_segment'] as $key => $segment) {
            $this->assertArrayNotHasKey('server_event', $segment);
        }
        $this->assertArrayHasKey('conversation_segment', $json);
        $this->assertEquals('prr', session()->get('conversation_index'));
    }

    public function test_unvalid_conversation_returns_error(): void
    {
        $User = $this->getRandomUser();

        $response = $this->actingAs($User)
            ->get('/conversation/next?person=notvalid&nextKey=r');

        $response->assertStatus(422);
        $json = $response->json();
        $this->assertArrayHasKey('error', $json);
    }

    public function test_unvalid_conversation_returns_error_when_starting(): void
    {
        $User = $this->getRandomUser();

        $response = $this->actingAs($User)
            ->get('/conversation/next?person=notvalid&is_starting=true');

        $response->assertStatus(422);
        $json = $response->json();
        $this->assertArrayHasKey('error', $json);
    }
}
