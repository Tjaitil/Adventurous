<?php

namespace Tests\Feature\Buildings;

use App\Models\ArmoryItemsData;
use App\Models\Inventory;
use App\Models\Soldier;
use App\Models\SoldierArmory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ArmoryTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['testing'];

    protected SoldierArmory $WarriorsMeleeArmory;

    protected SoldierArmory $WarriorsRangedArmory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();

        $Warrior = Soldier::factory()
            ->create([
                'username' => $this->RandomUser->username,
                'user_id' => $this->RandomUser->id,
                'type' => 'melee',
            ])->firstOrFail();

        $this->WarriorsMeleeArmory = SoldierArmory::factory()->count(1)
            ->state(
                [
                    'id' => $Warrior->id,
                    'warrior_id' => $Warrior->warrior_id,
                    'username' => $this->RandomUser->username,
                ]
            )
            ->create()
            ->firstOrFail();

        $Warrior = Soldier::factory()
            ->count(1)
            ->create([
                'username' => $this->RandomUser->username,
                'user_id' => $this->RandomUser->id,
                'type' => 'ranged',
            ])->firstOrFail();

        $this->WarriorsRangedArmory = SoldierArmory::factory()->count(1)
            ->state(
                [
                    'id' => $Warrior->id,
                    'warrior_id' => $Warrior->warrior_id,
                    'username' => $this->RandomUser->username,
                ])
            ->create()
            ->firstOrFail();

        $this->actingAs($this->RandomUser);
    }

    public function test_can_get_armory(): void
    {
        $response = $this->get('/armory');

        $response->assertStatus(200)
            ->assertViewIs('armory');
    }

    public function test_can_get_warriors(): void
    {
        $response = $this->get('/armory/soldiers');

        $response->assertStatus(200)
            ->json();
    }

    /**
     * The
     *
     * @dataProvider armoryProvider
     */
    public function test_can_add_armor(string $item): void
    {
        $this->insertItemToInventory($this->RandomUser, $item, 1);
        $ArmoryItemData = ArmoryItemsData::where('item', $item)->first();
        $part = $ArmoryItemData?->type->value;
        $response = $this->post('/armory/soldier/add', [
            'warrior_id' => $this->WarriorsMeleeArmory->warrior_id,
            'is_removing' => false,
            'item' => $item,
            'amount' => 1,
            'hand' => null,
        ]);

        $response->assertStatus(200)
            ->json();

        $this->WarriorsMeleeArmory->refresh();

        $this->assertEquals($item, $this->WarriorsMeleeArmory->{$part});
    }

    public static function armoryProvider(): array
    {
        return [
            'iron helm' => ['item' => 'iron helm'],
            'iron platebody' => ['item' => 'iron platebody'],
            'iron platelegs' => ['item' => 'iron platelegs'],
            'iron boots' => ['item' => 'iron boots'],
        ];
    }

    #[DataProvider('armoryProvider')]
    public function test_can_remove_armor(string $item): void
    {
        $ArmoryItemData = ArmoryItemsData::where('item', $item)->first();
        $part = $ArmoryItemData?->type->value;

        $this->WarriorsMeleeArmory->{$part} = $item;
        $this->WarriorsMeleeArmory->save();

        $response = $this->post('/armory/soldier/remove', [
            'warrior_id' => $this->WarriorsMeleeArmory->warrior_id,
            'is_removing' => true,
            'part' => $part,
        ]);

        $response->assertStatus(200)
            ->json();

        $this->WarriorsMeleeArmory->refresh();

        $this->assertEquals(null, $this->WarriorsMeleeArmory->{$part});
    }

    public static function rangedWeaponProvider(): array
    {
        return [
            'oak bow' => ['item' => 'oak bow'],
            'iron crossbow' => ['item' => 'iron crossbow'],
        ];
    }

    #[DataProvider('rangedWeaponProvider')]
    public function test_cannot_add_ranged_weapon_to_melee_warrior(string $item): void
    {
        $item = 'oak bow';
        $this->insertItemToInventory($this->RandomUser, $item, 1);

        $response = $this->post('/armory/soldier/add', [
            'warrior_id' => $this->WarriorsMeleeArmory->warrior_id,
            'is_removing' => false,
            'item' => $item,
            'amount' => 1,
            'hand' => 'right_hand',
        ]);

        $response->assertStatus(400)
            ->json();

        $this->assertDatabaseHas('warriors_armory',
            [
                'warrior_id' => $this->WarriorsMeleeArmory->warrior_id,
                'right_hand' => $this->WarriorsMeleeArmory->right_hand,
                'left_hand' => null,
            ]);
    }

    public function test_add_throwing_knives_removes_right_and_left_hand(): void
    {
        $item = 'adron throwing knives';
        $this->insertItemToInventory($this->RandomUser, $item, 1);

        $this->WarriorsRangedArmory->right_hand = 'oak bow';
        $this->WarriorsRangedArmory->save();

        $Inventory = Inventory::where('username', $this->RandomUser->username)
            ->where('item', 'oak bow')
            ->get();

        $response = $this->post('/armory/soldier/add', [
            'warrior_id' => $this->WarriorsRangedArmory->warrior_id,
            'is_removing' => false,
            'item' => $item,
            'amount' => 1,
            'hand' => null,
        ]);

        $response->assertStatus(200)
            ->json();

        $this->assertDatabaseHas('warriors_armory',
            [
                'warrior_id' => $this->WarriorsRangedArmory->warrior_id,
                'right_hand' => null,
                'ammunition' => $item,
                'ammunition_amount' => 1,
                'left_hand' => null,
            ]);

        $this->assertDatabaseHas('inventory',
            [
                'user_id' => $this->RandomUser->id,
                'item' => 'oak bow',
                'amount' => $Inventory->first()?->amount + 1 ?? 1,
            ]);
    }
}
