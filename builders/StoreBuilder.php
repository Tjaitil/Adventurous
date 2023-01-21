<?php

class StoreBuilder
{
    private StoreResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new StoreResource($resource);
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }

    /**
     * Set available item amount
     *
     * @param string $item Item name
     * @param int $amount New amount
     *
     * @return void
     */
    public function setAmount(string $item, int $amount)
    {
        foreach ($this->resource->list as $key => $item) {
            if ($item->name) {
                $item->amount = $amount;
                break;
            }
        }
    }

    public function build()
    {
        return $this->resource;
    }
}
