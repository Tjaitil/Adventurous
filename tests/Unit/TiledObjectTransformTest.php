<?php

namespace Tests\Unit;

use App\Services\TiledObjectTransform;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TiledObjectTransformTest extends TestCase
{
    /**
     * @param  array{float, float, float, float}  $expectedBox  x, y, width, height
     */
    #[DataProvider('boundingBoxProvider')]
    public function test_bounding_box(bool $isTile, float $rotation, array $expectedBox): void
    {
        $transform = new TiledObjectTransform(200, $isTile ? 40 : 100, 40, 60, $isTile, $rotation, false, false);

        $box = $transform->boundingBox();

        $this->assertEqualsWithDelta($expectedBox, [$box['x'], $box['y'], $box['width'], $box['height']], 0.0001);
    }

    /**
     * Tile y is the bottom at 100 (top 40), rectangle y is the top at 100.
     *
     * @return array<string, array{bool, float, array{float, float, float, float}}>
     */
    public static function boundingBoxProvider(): array
    {
        return [
            'tile 0' => [true, 0, [200, 40, 40, 60]],
            'tile 90' => [true, 90, [200, 100, 60, 40]],
            'tile 180' => [true, 180, [160, 100, 40, 60]],
            'tile -90' => [true, -90, [140, 60, 60, 40]],
            'tile 270 equals -90' => [true, 270, [140, 60, 60, 40]],
            'tile 30' => [true, 30, [200, 48.038476, 64.641016, 71.961524]],
            'rectangle 0' => [false, 0, [200, 100, 40, 60]],
            'rectangle 90' => [false, 90, [140, 100, 60, 40]],
            'rectangle 180' => [false, 180, [160, 40, 40, 60]],
            'rectangle -90' => [false, -90, [200, 60, 60, 40]],
        ];
    }

    public function test_rotation_leaves_no_floating_point_noise(): void
    {
        $transform = new TiledObjectTransform(200, 40, 40, 60, true, 90, false, false);

        $this->assertSame(
            ['x' => 200.0, 'y' => 100.0, 'width' => 60.0, 'height' => 40.0],
            $transform->boundingBox(),
        );
    }

    public function test_flip_x_mirrors_rectangle_inside_box(): void
    {
        $transform = new TiledObjectTransform(100, 50, 32, 32, true, 0, true, false);

        $rect = $transform->transformRect(0, 0, 8, 32);

        $this->assertSame(['x' => 124.0, 'y' => 50.0, 'width' => 8.0, 'height' => 32.0], $rect);
    }

    public function test_flip_y_with_half_turn_mirrors_x(): void
    {
        $transform = new TiledObjectTransform(100, 50, 32, 32, true, 180, false, true);

        $rect = $transform->transformRect(0, 0, 8, 32);

        $this->assertSame(['x' => 92.0, 'y' => 82.0, 'width' => 8.0, 'height' => 32.0], $rect);
    }

    public function test_untransformed_rectangle_is_only_offset(): void
    {
        $transform = new TiledObjectTransform(100, 50, 32, 32, true, 0, false, false);

        $rect = $transform->transformRect(3, 5, 8, 10);

        $this->assertSame(['x' => 103.0, 'y' => 55.0, 'width' => 8.0, 'height' => 10.0], $rect);
    }
}
