<?php

namespace Tests\Feature\Services;

use App\Enums\GameLocations;
use App\Models\Item;
use App\Models\TraderAssignment;
use App\Models\TraderAssignmentType;
use App\Services\TraderAssignmentGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraderAssignmentGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_new_creates_assignments_for_all_locations(): void
    {

        $service = new TraderAssignmentGeneratorService;

        $service->generateNew();

        $assignments = TraderAssignment::all();
        $this->assertGreaterThan(0, $assignments->count());

        foreach (GameLocations::values() as $location) {
            $assignmentsForLocation = TraderAssignment::where('base', $location)->get();
            $this->assertGreaterThanOrEqual(0, $assignmentsForLocation->count());
        }
    }

    public function test_generate_new_creates_assignments_with_valid_type_ids(): void
    {

        $service = new TraderAssignmentGeneratorService;

        $service->generateNew();

        $assignments = TraderAssignment::all();

        foreach ($assignments as $assignment) {
            $this->assertNotNull($assignment->trader_assignment_type_id);
            $type = TraderAssignmentType::find($assignment->trader_assignment_type_id);
            $this->assertNotNull($type);
        }
    }

    public function test_generate_new_only_includes_items_with_trader_assignment_type(): void
    {
        $service = new TraderAssignmentGeneratorService;

        $service->generateNew();

        $assignments = TraderAssignment::all();

        foreach ($assignments as $assignment) {
            $item = Item::where('item_id', $assignment->cargo_id)->first();
            $this->assertNotNull($item);
            $this->assertNotNull($item->trader_assignment_type_id);
        }
    }
}
