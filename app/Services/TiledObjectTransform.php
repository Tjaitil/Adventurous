<?php

namespace App\Services;

/**
 * Placement of a Tiled object after flips and rotation.
 *
 * Tiled flips an object inside its own box first, then rotates it clockwise
 * around the object's x/y. Tile objects (with a gid) have that origin at the
 * bottom-left, other objects such as rectangles at the top-left.
 */
final class TiledObjectTransform
{
    private float $cos;

    private float $sin;

    private float $pivotY;

    /**
     * @param  float  $x  Left edge of the unrotated object
     * @param  float  $y  Top edge of the unrotated object
     */
    public function __construct(
        private float $x,
        private float $y,
        private float $width,
        private float $height,
        bool $isTile,
        float $rotation,
        private bool $flipX,
        private bool $flipY,
    ) {
        $radians = deg2rad($rotation);
        $this->cos = round(cos($radians), 10);
        $this->sin = round(sin($radians), 10);
        $this->pivotY = $isTile ? $height : 0.0;
    }

    /**
     * @return array{x: float, y: float, width: float, height: float}
     */
    public function boundingBox(): array
    {
        return $this->transformRect(0, 0, $this->width, $this->height);
    }

    /**
     * Bounding box in map coordinates of a rectangle given relative to the
     * top-left of the unrotated object.
     *
     * @return array{x: float, y: float, width: float, height: float}
     */
    public function transformRect(float $x, float $y, float $width, float $height): array
    {
        $xs = [];
        $ys = [];
        $corners = [[$x, $y], [$x + $width, $y], [$x + $width, $y + $height], [$x, $y + $height]];
        foreach ($corners as [$cornerX, $cornerY]) {
            [$mapX, $mapY] = $this->transformPoint($cornerX, $cornerY);
            $xs[] = $mapX;
            $ys[] = $mapY;
        }

        return [
            'x' => round(min($xs), 6),
            'y' => round(min($ys), 6),
            'width' => round(max($xs) - min($xs), 6),
            'height' => round(max($ys) - min($ys), 6),
        ];
    }

    /**
     * @return array{float, float}
     */
    private function transformPoint(float $x, float $y): array
    {
        $flippedX = $this->flipX ? $this->width - $x : $x;
        $flippedY = $this->flipY ? $this->height - $y : $y;
        $fromPivotY = $flippedY - $this->pivotY;

        return [
            $this->x + $flippedX * $this->cos - $fromPivotY * $this->sin,
            $this->y + $this->pivotY + $flippedX * $this->sin + $fromPivotY * $this->cos,
        ];
    }
}
