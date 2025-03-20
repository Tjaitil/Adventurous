<?php

use App\Http\Resources\StoreItemResource;
use App\Http\Resources\StoreResource;
use App\Stores\SmithyStore;
use Tests\TestCase;

final class SmithyStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_make_store()
    {
        /**
         * @var SmithyStore $SmithyStore
         */
        $SmithyStore = app()->make(SmithyStore::class);
        $StoreResource = $SmithyStore->makeStore($this->RandomUser);
        $this->assertEquals($StoreResource::class, StoreResource::class);
    }

    public function test_to_store_item_response()
    {
        /**
         * @var SmithyStore $SmithyStore
         */
        $SmithyStore = app()->make(SmithyStore::class);
        $storeResource = $SmithyStore->makeStore($this->RandomUser);
        $response = $SmithyStore->toStoreItemResponse($storeResource);

        $keys = array_keys((new StoreItemResource([]))->toArray());

        $this->assertEqualsCanonicalizing([
            ...$keys
        ], array_keys($response->getData(true)['data']['store_items'][0]));
    }
}
