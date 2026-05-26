<?php

namespace Tests\Unit;

use App\Services\WorldLoaderService;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group(('mapx'))]
class WorldLoaderServiceTest extends TestCase
{
    private WorldLoaderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('gamedata');

        Storage::disk('gamedata')->put('buildings.json', json_encode(['tiles' => []]));
        Storage::disk('gamedata')->put('landscape.json', json_encode(['tiles' => []]));

        $this->service = new WorldLoaderService(Storage::disk('gamedata'));
    }

    public function test_set_and_get_map(): void
    {
        $this->service->setMap('4.3');

        $this->assertSame('4.3', $this->service->getMap());
    }

    public function test_get_world_data_returns_false_when_map_file_missing(): void
    {
        $this->service->setMap('99.99');

        $result = $this->service->getWorldData();

        $this->assertFalse($result);
    }

    public function test_get_world_data_returns_correct_structure(): void
    {
        $this->putMapFile('3.1', []);

        $this->service->setMap('3.1');
        $result = $this->service->getWorldData();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertSame('3.1', $result['data']['current_map']);
        $this->assertArrayHasKey('map_data', $result['data']);
        $this->assertArrayHasKey('objects', $result['data']['map_data']);
    }

    public function test_character_has_conversation_true_when_file_exists(): void
    {
        Storage::disk('gamedata')->put('conversations/kapys.json', json_encode(['index' => 'k']));

        $this->putMapFile('2.1', [
            $this->makeCharacterObject(src: 'kapys.png'),
        ]);

        $this->service->setMap('2.1');
        $result = $this->service->getWorldData();

        $character = $this->findObjectBySrc($result, 'kapys.png');
        $this->assertTrue($character['hasConversation']);
    }

    public function test_character_has_conversation_false_when_file_missing(): void
    {
        $this->putMapFile('2.2', [
            $this->makeCharacterObject(src: 'unknown_npc.png'),
        ]);

        $this->service->setMap('2.2');
        $result = $this->service->getWorldData();

        $character = $this->findObjectBySrc($result, 'unknown_npc.png');
        $this->assertFalse($character['hasConversation']);
    }

    #[DataProvider('characterDisplayNameProvider')]
    public function test_character_display_name_is_mapped_correctly(string $src, string $expectedDisplayName): void
    {
        $this->putMapFile('2.3', [
            $this->makeCharacterObject(src: $src),
        ]);

        $this->service->setMap('2.3');
        $result = $this->service->getWorldData();

        $character = $this->findObjectBySrc($result, $src);
        $this->assertSame($expectedDisplayName, $character['displayName']);
    }

    public static function characterDisplayNameProvider(): array
    {
        return [
            'woman character' => ['Woman character.png', 'citizen'],
            'Character13' => ['Character13.png', 'woman'],
            'Citizen' => ['Citizen.png', 'citizen'],
            'wujkin soldier' => ['wujkin soldier.png', 'soldier'],
            'Fansal male' => ['Fansal male v2.png', 'Fansal male'],
            'tutorial sailor' => ['tutorial_sailor.png', 'tutorial_sailer'],
            'default (kapys)' => ['kapys.png', 'kapys'],
        ];
    }

    #[DataProvider('buildingDisplayNameProvider')]
    public function test_building_display_name_is_normalised(string $src, string $expectedDisplayName): void
    {
        $this->putMapFile('3.2', [], buildings: [
            $this->makeBuildingObject(src: $src),
        ]);

        $this->service->setMap('3.2');
        $result = $this->service->getWorldData();

        $building = $this->findObjectBySrc($result, $src);
        $this->assertSame($expectedDisplayName, $building['displayName']);
    }

    public static function buildingDisplayNameProvider(): array
    {
        return [
            'archery shop' => ['archery shop.png', 'archeryshop'],
            'workforce lodge' => ['workforce lodge.png', 'workforcelodge'],
            'stockpile desert' => ['stockpile desert.png', 'stockpile'],
            'merchant desert' => ['merchant desert.png', 'merchant'],
            'adventures base' => ['adventures base desert.png', 'adventures'],
            'plain building (src)' => ['tavern.png', 'tavern'],
        ];
    }

    public function test_building_src_is_normalised_for_city_centre(): void
    {
        $this->putMapFile('3.3', [], buildings: [
            $this->makeBuildingObject(src: 'city centre.png'),
        ]);

        $this->service->setMap('3.3');
        $result = $this->service->getWorldData();

        $building = $this->findObjectBySrc($result, 'citycentre.png');
        $this->assertNotNull($building, 'Expected src to be normalised to citycentre.png');
        $this->assertSame('citycentre', $building['displayName']);
    }

    public function test_rotation_90_swaps_width_and_height_and_adjusts_x(): void
    {
        $this->putMapFile('4.1', [
            $this->makeCharacterObject(src: 'kapys.png', x: 200, y: 100, width: 40, height: 60, rotation: 90),
        ]);

        $this->service->setMap('4.1');
        $result = $this->service->getWorldData();

        $character = $this->findObjectBySrc($result, 'kapys.png');
        $this->assertSame(60, $character['width']);
        $this->assertSame(40, $character['height']);
        $this->assertSame(140.0, $character['x']);
    }

    private function putMapFile(string $map, array $characterObjects, array $buildings = []): void
    {
        $layers = [];

        if (! empty($characterObjects)) {
            $layers[] = ['name' => 'Characters', 'objects' => $characterObjects];
        }

        if (! empty($buildings)) {
            $layers[] = ['name' => 'Buildings', 'objects' => $buildings];
        }

        Storage::disk('gamedata')->put($map.'.json', json_encode(['layers' => $layers]));
    }

    private function makeCharacterObject(
        string $src,
        int $x = 100,
        int $y = 200,
        int $width = 42,
        int $height = 42,
        int $rotation = 0,
    ): array {
        return [
            'gid' => 1,
            'id' => 1,
            'name' => '',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'rotation' => $rotation,
            'type' => 'object',
            'visible' => true,
            'properties' => [
                ['name' => 'type', 'type' => 'string', 'value' => 'character'],
                ['name' => 'src',  'type' => 'string', 'value' => $src],
            ],
        ];
    }

    private function makeBuildingObject(
        string $src,
        int $x = 200,
        int $y = 300,
        int $width = 128,
        int $height = 128,
        int $rotation = 0,
    ): array {
        return [
            'gid' => 1,
            'id' => 2,
            'name' => '',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'rotation' => $rotation,
            'type' => 'object',
            'visible' => true,
            'properties' => [
                ['name' => 'type', 'type' => 'string', 'value' => 'building'],
                ['name' => 'src',  'type' => 'string', 'value' => $src],
            ],
        ];
    }

    private function findObjectBySrc(array|false $result, string $src): ?array
    {
        $this->assertIsArray($result, 'getWorldData() returned false — check map file was written correctly');

        foreach ($result['data']['map_data']['objects'] as $object) {
            if (($object['src'] ?? null) === $src) {
                return $object;
            }
        }

        return null;
    }
}
