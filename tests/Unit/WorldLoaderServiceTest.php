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

    /**
     * @param  array{float, float, float, float}|null  $expectedBox  xRotated, yRotated, widthRotated, heightRotated
     */
    #[DataProvider('tileRotationProvider')]
    public function test_tile_rotates_around_bottom_left(int $rotation, ?array $expectedBox): void
    {
        $this->putMapFile('4.1', [
            $this->makeCharacterObject(src: 'kapys.png', x: 200, y: 100, width: 40, height: 60, rotation: $rotation),
        ]);

        $this->service->setMap('4.1');
        $character = $this->findObjectBySrc($this->service->getWorldData(), 'kapys.png');

        $this->assertSame([200.0, 40.0, 40, 60], [(float) $character['x'], (float) $character['y'], $character['width'], $character['height']]);
        $this->assertRotatedBox($expectedBox, $character);
        $this->assertSame($rotation, $character['rotation']);
    }

    /**
     * @param  array{float, float, float, float}|null  $expectedBox  xRotated, yRotated, widthRotated, heightRotated
     * @param  array<mixed>  $object
     */
    private function assertRotatedBox(?array $expectedBox, array $object): void
    {
        $actual = [$object['xRotated'], $object['yRotated'], $object['widthRotated'], $object['heightRotated']];

        if ($expectedBox === null) {
            $this->assertSame([null, null, null, null], $actual);

            return;
        }

        $this->assertEqualsWithDelta($expectedBox, $actual, 0.001);
    }

    /**
     * @return array<string, array{int, array{float, float, float, float}|null}>
     */
    public static function tileRotationProvider(): array
    {
        return [
            'no rotation' => [0, null],
            '90' => [90, [200, 100, 60, 40]],
            '180' => [180, [160, 100, 40, 60]],
            '-90' => [-90, [140, 60, 60, 40]],
        ];
    }

    /**
     * @param  array{float, float, float, float}|null  $expectedBox  xRotated, yRotated, widthRotated, heightRotated
     */
    #[DataProvider('rectangleRotationProvider')]
    public function test_rectangle_rotates_around_top_left(int $rotation, ?array $expectedBox): void
    {
        $this->putMapFile('4.1', [], [], [
            $this->makeFigureObject(x: 200, y: 100, width: 40, height: 60, rotation: $rotation),
        ]);

        $this->service->setMap('4.1');
        $figure = $this->findObjectById($this->service->getWorldData(), 3);

        $this->assertSame([200.0, 100.0, 40, 60], [(float) $figure['x'], (float) $figure['y'], $figure['width'], $figure['height']]);
        $this->assertRotatedBox($expectedBox, $figure);
    }

    /**
     * @return array<string, array{int, array{float, float, float, float}|null}>
     */
    public static function rectangleRotationProvider(): array
    {
        return [
            'no rotation' => [0, null],
            '90' => [90, [140, 100, 60, 40]],
            '180' => [180, [160, 40, 40, 60]],
            '-90' => [-90, [200, 60, 60, 40]],
        ];
    }

    public function test_preset_collision_is_mirrored_with_flipped_and_rotated_tile(): void
    {
        Storage::disk('gamedata')->put('landscape.json', json_encode(['tiles' => [[
            'id' => 0,
            'image' => 'pole.png',
            'properties' => [['name' => 'type', 'type' => 'string', 'value' => '']],
            'objectgroup' => ['objects' => [['x' => 0, 'y' => 0, 'width' => 8, 'height' => 32]]],
        ]]]));
        $service = new WorldLoaderService(Storage::disk('gamedata'));
        $pole = $this->makeTileObject(src: 'pole.png', x: 100, y: 82, width: 32, height: 32, rotation: 180);
        $pole['gid'] = 0x40000000 | 263;
        $this->putMapFile('4.1', [], [], [], [$pole]);

        $service->setMap('4.1');
        $loaded = $this->findObjectBySrc($service->getWorldData(), 'pole.png');

        $this->assertRotatedBox([68, 82, 32, 32], $loaded);
        $this->assertEqualsWithDelta(
            [92, 100, 82, 114],
            [$loaded['diameterLeft'], $loaded['diameterRight'], $loaded['diameterUp'], $loaded['diameterDown']],
            0.001,
        );
    }

    #[DataProvider('flipProvider')]
    public function test_flip_flags_are_decoded_from_gid(int $gid, int $rotation, bool $expectedFlipX, bool $expectedFlipY): void
    {
        $object = $this->makeCharacterObject(src: 'pole.png', rotation: $rotation);
        $object['gid'] = $gid;
        $this->putMapFile('4.2', [$object]);

        $this->service->setMap('4.2');
        $result = $this->service->getWorldData();

        $loaded = $this->findObjectBySrc($result, 'pole.png');
        $this->assertSame($expectedFlipX, $loaded['flipX']);
        $this->assertSame($expectedFlipY, $loaded['flipY']);
        $this->assertSame($gid, $loaded['gid']);
        $this->assertSame($rotation, $loaded['rotation']);
    }

    /**
     * @return array<string, array{int, int, bool, bool}>
     */
    public static function flipProvider(): array
    {
        return [
            'no flip' => [263, 0, false, false],
            'horizontal bit' => [0x80000000 | 263, 0, true, false],
            'vertical bit' => [0x40000000 | 263, 0, false, true],
            'both bits' => [0xC0000000 | 263, 0, true, true],
            'rotation does not change flips' => [0x40000000 | 263, 180, false, true],
            'plain rotation has no flips' => [263, 180, false, false],
        ];
    }

    private function putMapFile(string $map, array $characterObjects, array $buildings = [], array $figures = [], array $objects = []): void
    {
        $layers = [];

        if (! empty($characterObjects)) {
            $layers[] = ['name' => 'Characters', 'objects' => $characterObjects];
        }

        if (! empty($buildings)) {
            $layers[] = ['name' => 'Buildings', 'objects' => $buildings];
        }

        if (! empty($figures)) {
            $layers[] = ['name' => 'Figures', 'objects' => $figures];
        }

        if (! empty($objects)) {
            $layers[] = ['name' => 'Objects', 'objects' => $objects];
        }

        Storage::disk('gamedata')->put($map.'.json', json_encode(['layers' => $layers]));
    }

    private function makeTileObject(string $src, int $x, int $y, int $width, int $height, int $rotation = 0): array
    {
        return [
            'gid' => 1,
            'id' => 4,
            'name' => '',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'rotation' => $rotation,
            'type' => '',
            'visible' => true,
            'properties' => [
                ['name' => 'src', 'type' => 'string', 'value' => $src],
                ['name' => 'type', 'type' => 'string', 'value' => ''],
            ],
        ];
    }

    private function makeFigureObject(int $x, int $y, int $width, int $height, int $rotation = 0): array
    {
        return [
            'id' => 3,
            'name' => '',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'rotation' => $rotation,
            'type' => 'figure',
            'visible' => true,
        ];
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

    private function findObjectById(array|false $result, int $id): ?array
    {
        $this->assertIsArray($result, 'getWorldData() returned false — check map file was written correctly');

        foreach ($result['data']['map_data']['objects'] as $object) {
            if (($object['id'] ?? null) === $id) {
                return $object;
            }
        }

        return null;
    }
}
