<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;

final class WorldLoaderService
{
    private const FLIP_HORIZONTAL = 0x80000000;

    private const FLIP_VERTICAL = 0x40000000;

    /**
     * @var array<mixed>
     */
    private array $objectCollisionData = [];

    private string $map;

    /**
     * @var array<mixed>
     */
    private array $object_array = [];

    public function __construct(private Filesystem $disk)
    {
        $buildingsArray = json_decode($this->disk->get('buildings.json') ?? '', true);
        $buildingsArray = array_filter($buildingsArray['tiles'], function ($object) {
            return isset($object['properties']);
        });
        $landscapeArray = json_decode($this->disk->get('landscape.json') ?? '', true);
        $landscapeArray = array_filter($landscapeArray['tiles'], function ($object) {
            return isset($object['properties']);
        });
        $this->objectCollisionData = array_merge($buildingsArray, $landscapeArray);
    }

    public function setMap(string $map): void
    {
        $this->map = $map;
    }

    public function getMap(): string
    {
        return $this->map;
    }

    /**
     * @return array<mixed>|false
     */
    public function getWorldData(): array|false
    {
        $result = $this->loadObjects();
        if ($result === false) {
            return false;
        }

        return [
            'data' => [
                'current_map' => strval($this->map),
                'changed_location' => '',
                'map_data' => $this->object_array,
                'events' => [],
            ],
        ];
    }

    public function loadObjects(): bool
    {
        $file = $this->disk->get($this->map.'.json');
        if (is_null($file)) {
            return false;
        }

        $string = json_decode($file, true);

        $objects = [];
        $objects['objects'] = [];
        $objects['buildings'] = [];

        for ($i = 0; $i < count($string['layers']); $i++) {
            if (in_array($string['layers'][$i]['name'], ['Objects', 'Buildings', 'Characters', 'Figures']) === true) {
                $object_array = $string['layers'][$i]['objects'];
                for ($x = 0; $x < count($string['layers'][$i]['objects']); $x++) {
                    $object_array[$x] = $this->applyFlips($object_array[$x]);
                    unset($object_array[$x]['name']);
                    // If the object has any properties, move the data up in the array
                    if (isset($object_array[$x]['properties'])) {
                        $property_array = $object_array[$x]['properties'];
                        for ($y = 0; $y < count($property_array); $y++) {
                            $object_array[$x][$property_array[$y]['name']] = $property_array[$y]['value'];
                            unset($object_array[$x]['properties']);
                        }
                    }
                    if ($string['layers'][$i]['name'] === 'Buildings') {
                        $object_array[$x] = $this->setupBuilding($object_array[$x]);
                    }
                    // Y value in images in json files are the value at the bottom, not the top
                    if (in_array($object_array[$x]['type'], ['figure', 'object'])) {
                        $object_array[$x]['y'] = round($object_array[$x]['y'], 2);
                    } elseif ($object_array[$x]['type'] === 'daqloon_fighting_area') {
                        $object_array[$x] = $this->applyRotatedBox($object_array[$x], $this->makeTransform($object_array[$x]));
                        $box = $this->getRotatedBox($object_array[$x]);
                        $object_array[$x]['y'] = round($object_array[$x]['y']);
                        $object_array[$x]['x'] = round($object_array[$x]['x']);
                        $object_array[$x]['diameterUp'] = round($box['y']);
                        $object_array[$x]['diameterDown'] = round($box['y']) + $box['height'];
                        $object_array[$x]['diameterLeft'] = round($box['x']);
                        $object_array[$x]['diameterRight'] = round($box['x']) + $box['width'];
                        $objects['daqloon_fighting_areas'][] = $object_array[$x];
                        unset($object_array[$x]);

                        continue;
                    } else {
                        $object_array[$x]['y'] = round($object_array[$x]['y'], 2) - $object_array[$x]['height'];
                    }
                    $transform = $this->makeTransform($object_array[$x]);
                    $object_array[$x] = $this->applyRotatedBox($object_array[$x], $transform);
                    if (isset($object_array[$x]['src'])) {
                        $objectSrc = $object_array[$x]['src'];
                        $objectCollData = array_values(array_filter($this->objectCollisionData, function ($object) use ($objectSrc) {
                            return $object['image'] == $objectSrc;
                        }));
                    }
                    if (! isset($object_array[$x]['diameterUp'])) {
                        $object_array[$x]['diameterUp'] = 0;
                        $object_array[$x]['diameterRight'] = 0;
                        $object_array[$x]['diameterDown'] = 0;
                        $object_array[$x]['diameterLeft'] = 0;
                    }
                    $object_array[$x]['x'] = round($object_array[$x]['x'], 2);
                    $box = $this->getRotatedBox($object_array[$x]);
                    // Check whether there is a diameter preset for this object
                    if (isset($objectCollData[0]['objectgroup']['objects'])) {
                        $objectCollData = $objectCollData[0]['objectgroup']['objects'][0];
                        $collision = $transform->transformRect(
                            $objectCollData['x'],
                            $objectCollData['y'],
                            $objectCollData['width'],
                            $objectCollData['height'],
                        );
                        $object_array[$x]['diameterUp'] = $collision['y'];
                        $object_array[$x]['diameterDown'] = $collision['y'] + $collision['height'];
                        $object_array[$x]['diameterLeft'] = $collision['x'];
                        $object_array[$x]['diameterRight'] = $collision['x'] + $collision['width'];
                    } elseif ($string['layers'][$i]['name'] === 'Buildings') {
                        $object_array[$x]['diameterUp'] = $box['y'] + 40;
                        $object_array[$x]['diameterDown'] = $box['y'] + $box['height'];
                        $object_array[$x]['diameterLeft'] = $box['x'];
                        $object_array[$x]['diameterRight'] = $box['x'] + $box['width'];
                    } else {
                        if (! in_array($object_array[$x]['type'], [
                            'figure', 'start_point', 'daqloon_fighting_area',
                            'desert_dune', 'nc_object', '',
                        ])) {
                            switch ($box['height']) {
                                case 32:
                                    $object_array[$x]['diameterUp'] = $box['y'] + 10;
                                    break;
                                default:
                                    $object_array[$x]['diameterUp'] = $box['y'];
                                    break;
                            }
                            if (in_array($object_array[$x]['src'], ['crate_3.png', 'crate_5.png'])) {
                                $object_array[$x]['diameterUp'] = $box['y'] + 10;
                            }
                        } else {
                            $object_array[$x]['diameterUp'] = $box['y'];
                        }
                        $object_array[$x]['diameterDown'] = $box['y'] + $box['height'];
                        $object_array[$x]['diameterLeft'] = $box['x'];
                        $object_array[$x]['diameterRight'] = $box['x'] + $box['width'];
                    }
                    if ($object_array[$x]['type'] == 'character') {
                        $object_array[$x] = $this->setupCharacter($object_array[$x]);
                    }
                    $objects['objects'][] = $object_array[$x];
                }
            }
            if ($string['layers'][$i]['name'] === 'Sides/Corners') {
                $object_array = $string['layers'][$i]['objects'];
                $objects['daqloon_fighting_areas'] = array_filter($object_array, function ($object) {
                    return $object['type'] === 'daqloon_fighting_area';
                });
            }
        }
        $this->object_array = $objects;

        return true;
    }

    /**
     * @param  array<mixed>  $object
     * @return array<mixed>
     */
    private function setupBuilding(array $object): array
    {
        /**
         * TODO: rework of map inconsistencies
         */
        switch ($object['src']) {
            case 'city centre.png':
                $object['src'] = 'citycentre.png';
                $object['displayName'] = 'citycentre';
                break;
            case 'army camp.png':
                $object['src'] = 'armycamp.png';
                $object['displayName'] = 'armycamp';
                break;
            case 'archery shop.png':
                $object['displayName'] = 'archeryshop';
                break;
            case 'adventure base.png':
            case 'adventures base desert.png':
                $object['displayName'] = 'adventures';
                break;
            case 'stockpile desert.png':
                $object['displayName'] = 'stockpile';
                break;
            case 'merchant desert.png':
                $object['displayName'] = 'merchant';
                break;
            case 'workforce lodge.png':
                $object['displayName'] = 'workforcelodge';
                break;
        }
        if (! isset($object['displayName']) || empty($object['displayName'])) {
            $object['displayName'] = trim(substr($object['src'], 0, -4));
        }
        if ($this->map === '9.9') {
            $object['visible'] = false;
        }

        return $object;
    }

    /**
     * @param  array<mixed>  $object
     * @return array<mixed>
     */
    private function setupCharacter(array $object): array
    {
        $display_name = match ($object['src']) {
            'Woman character.png' => 'citizen',
            'Character13.png' => 'woman',
            'Citizen.png' => 'citizen',
            'wujkin soldier.png' => 'soldier',
            'Fansal male v2.png' => 'Fansal male',
            'tutorial_sailor.png' => 'tutorial_sailer',
            default => substr($object['src'], 0, -4),
        };
        $object['displayName'] = $display_name;

        $object['hasConversation'] = $this->disk->exists('conversations/'.$display_name.'.json');

        return $object;
    }

    /**
     * Placement of the unrotated object. Expects y to be the top edge.
     *
     * @param  array<mixed>  $object
     */
    private function makeTransform(array $object): TiledObjectTransform
    {
        return new TiledObjectTransform(
            round((float) $object['x'], 2),
            (float) $object['y'],
            (float) $object['width'],
            (float) $object['height'],
            isset($object['gid']),
            (float) ($object['rotation'] ?? 0),
            $object['flipX'] ?? false,
            $object['flipY'] ?? false,
        );
    }

    /**
     * x, y, width and height keep the unrotated Tiled values. The box of a
     * rotated object is added as xRotated, yRotated, widthRotated and
     * heightRotated, all null when the object is not rotated.
     *
     * @param  array<mixed>  $object
     * @return array<mixed>
     */
    private function applyRotatedBox(array $object, TiledObjectTransform $transform): array
    {
        $object['xRotated'] = null;
        $object['yRotated'] = null;
        $object['widthRotated'] = null;
        $object['heightRotated'] = null;

        if (($object['rotation'] ?? 0) == 0) {
            return $object;
        }

        $box = $transform->boundingBox();
        $object['xRotated'] = round($box['x'], 2);
        $object['yRotated'] = $box['y'];
        $object['widthRotated'] = $box['width'];
        $object['heightRotated'] = $box['height'];

        return $object;
    }

    /**
     * The rotated box when set, otherwise the plain x, y, width and height.
     *
     * @param  array<mixed>  $object
     * @return array{x: float|int, y: float|int, width: float|int, height: float|int}
     */
    private function getRotatedBox(array $object): array
    {
        return [
            'x' => $object['xRotated'] ?? $object['x'],
            'y' => $object['yRotated'] ?? $object['y'],
            'width' => $object['widthRotated'] ?? $object['width'],
            'height' => $object['heightRotated'] ?? $object['height'],
        ];
    }

    /**
     * Tiled stores flips in the top bits of the gid, separately from rotation.
     * Flips are applied before the rotation.
     *
     * @param  array<mixed>  $object
     * @return array<mixed>
     */
    private function applyFlips(array $object): array
    {
        $gid = (int) ($object['gid'] ?? 0);

        $object['flipX'] = ($gid & self::FLIP_HORIZONTAL) !== 0;
        $object['flipY'] = ($gid & self::FLIP_VERTICAL) !== 0;

        return $object;
    }
}
