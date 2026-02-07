<?php

namespace App\Services;

use App\Enums\GameLocations;
use App\Enums\TraderAssignmentTypeName;
use App\Models\Item;
use App\Models\TraderAssignment;
use App\Models\TraderAssignmentType;
use Illuminate\Support\Collection;

class TraderAssignmentGeneratorService
{
    /**
     * @var Collection<value-of<TraderAssignmentTypeName>, Collection<int, Item>>
     */
    private readonly Collection $cargoTypeMapping;

    public function __construct()
    {
        $this->cargoTypeMapping = $this->getCargoMapping();
    }

    public function generateNew(): void
    {
        $locationMapping = $this->getDestinationMapping();

        foreach ($locationMapping as $key => $value) {
            $this->generateAssignmentsForDestination($key, $value);
        }

    }

    /**
     * @param  array<int, string>  $availableBases
     * @return array<int, TraderAssignment>
     */
    public function generateAssignmentsForDestination(string $base, array $availableBases): array
    {

        $types = [
            TraderAssignmentTypeName::SMALL,
            TraderAssignmentTypeName::MEDIUM,
            TraderAssignmentTypeName::LARGE,
            TraderAssignmentTypeName::FAVOR,
        ];

        $assignments = [];

        foreach ($types as $typeName) {
            $type = TraderAssignmentType::where('type', $typeName->value)->first();
            $previousItemsIds = collect();
            if ($type) {

                for ($i = 0; $i < rand(1, 2); $i++) {

                    if (count($availableBases) === 0) {
                        continue;
                    }

                    $assignmentCargo = $this->getCargoForAssignment($typeName, $previousItemsIds);
                    if ($assignmentCargo === null) {
                        continue;
                    }
                    $previousItemsIds->push($assignmentCargo->item_id);

                    $assignments[] = TraderAssignment::create([
                        'destination' => $availableBases[array_rand($availableBases)],
                        'trader_assignment_type_id' => $type->id,
                        'base' => $base,
                        'cargo_id' => $assignmentCargo->item_id,
                        'assignment_amount' => $this->getCargoAmountForAssignmentType($typeName),
                        'time' => 60,
                        'date_inserted' => now(),
                    ]);

                }
            }
        }

        return $assignments;
    }

    private function getCargoAmountForAssignmentType(TraderAssignmentTypeName $type): int
    {
        return match ($type) {
            TraderAssignmentTypeName::SMALL => rand(30, 50),
            TraderAssignmentTypeName::MEDIUM => rand(60, 80),
            TraderAssignmentTypeName::LARGE => rand(110, 200),
            TraderAssignmentTypeName::FAVOR => rand(300, 400),
        };
    }

    /**
     * @return array<string, array<string>>
     */
    private function getDestinationMapping(): array
    {
        $mapping = [];

        foreach (GameLocations::values() as $key => $value) {
            $locationExceptsSelf = collect(GameLocations::values())->filter(function ($v) use ($value) {
                if ($v === $value) {
                    return false;
                }

                if (in_array($value, GameLocations::getDiplomacyLocations()) && in_array($v, GameLocations::getDiplomacyLocations())) {
                    return false;
                }

                return true;
            })->values()->toArray();

            $mapping[$value] = $locationExceptsSelf;
        }

        return $mapping;
    }

    /**
     * @return Collection<value-of<TraderAssignmentTypeName>, Collection<int, Item>>
     */
    private function getCargoMapping(): Collection
    {
        $Items = Item::whereNotNull('trader_assignment_type_id')
            ->with('traderAssignmentType')
            ->get();

        /**
         * PHPstan does not understand the groupBy with enum
         *
         * @var Collection<value-of<TraderAssignmentTypeName>, Collection<int, Item>>
         */
        $Items = $Items->groupBy(fn (Item $item) => $item->traderAssignmentType?->type->value ?? null)->filter();

        return $Items;
    }

    /**
     * @return Collection<int, Item>
     *
     * @throws \RuntimeException
     */
    private function getCargoMappingForAssignmentType(TraderAssignmentTypeName $type): Collection
    {
        if ($type === TraderAssignmentTypeName::SMALL) {
            $mapping = $this->cargoTypeMapping->get(TraderAssignmentTypeName::SMALL->value);
            if (is_null($mapping) || $mapping->isEmpty()) {
                throw new \RuntimeException('No cargo mapping found for assignment type: '.TraderAssignmentTypeName::SMALL->value);
            }

            return $mapping;
        } elseif ($type === TraderAssignmentTypeName::FAVOR) {
            $randomTypeMapping = $this->cargoTypeMapping->random();
            if (is_null($randomTypeMapping)) {
                throw new \RuntimeException('No cargo mapping available for favor type');
            }

            return $randomTypeMapping;
        } elseif ($type === TraderAssignmentTypeName::MEDIUM) {
            $rngType = rand(0, 10);

            if ($rngType <= 3) {
                $mapping = $this->cargoTypeMapping->get(TraderAssignmentTypeName::SMALL->value);
                if (is_null($mapping)) {
                    throw new \RuntimeException('No cargo mapping found for assignment type: '.TraderAssignmentTypeName::SMALL->value);
                }

                return $mapping;
            } else {
                $mapping = $this->cargoTypeMapping->get(TraderAssignmentTypeName::MEDIUM->value);
                if (is_null($mapping)) {
                    throw new \RuntimeException('No cargo mapping found for assignment type: '.TraderAssignmentTypeName::MEDIUM->value);
                }

                return $mapping;
            }
        } else {
            $rngType = rand(0, 10);

            if ($rngType <= 1) {
                $mapping = $this->cargoTypeMapping->get(TraderAssignmentTypeName::SMALL->value);
                if (is_null($mapping)) {
                    throw new \RuntimeException('No cargo mapping found for assignment type: '.TraderAssignmentTypeName::SMALL->value);
                }

                return $mapping;
            } elseif ($rngType <= 4) {
                $mapping = $this->cargoTypeMapping->get(TraderAssignmentTypeName::MEDIUM->value);
                if (is_null($mapping)) {
                    throw new \RuntimeException('No cargo mapping found for assignment type: '.TraderAssignmentTypeName::MEDIUM->value);
                }

                return $mapping;
            } else {
                $mapping = $this->cargoTypeMapping->get(TraderAssignmentTypeName::LARGE->value);
                if (is_null($mapping)) {
                    throw new \RuntimeException('No cargo mapping found for assignment type: '.TraderAssignmentTypeName::LARGE->value);
                }

                return $mapping;
            }
        }
    }

    /**
     * @param  Collection<int, mixed>  $previousItemsIds
     */
    private function getCargoforAssignment(TraderAssignmentTypeName $type, Collection $previousItemsIds): ?Item
    {
        $cargoTypeMapping = $this->getCargoMappingForAssignmentType($type);

        return $this->getRandomItemExcludingPrevious($cargoTypeMapping, $previousItemsIds);
    }

    /**
     * @param  Collection<int, Item>  $cargoTypeMapping
     * @param  Collection<int, mixed>  $previousItemsIds
     */
    private function getRandomItemExcludingPrevious(Collection $cargoTypeMapping, Collection $previousItemsIds): ?Item
    {
        $tries = 0;
        $item = $cargoTypeMapping->random();

        while ($previousItemsIds->contains($item->item_id)) {
            $tries++;
            if ($tries > 10) {
                $item = null;
                break;
            }
            $item = $cargoTypeMapping->random();
        }

        return $item;
    }
}
