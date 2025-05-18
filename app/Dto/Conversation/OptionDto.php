<?php

namespace App\Dto\Conversation;

final class OptionDto
{
    public readonly int $id;

    public string $text;

    public readonly string $person;

    public readonly ?string $container;

    /**
     * @var array<string, string>
     */
    public readonly array $optionValues;

    public readonly string $nextKey;

    public readonly bool $hasConditional;

    public readonly bool $hasClientCallback;

    public readonly bool $hasReplacer;

    public ?string $clientCallback = null;

    /**
     * @param  array<string, mixed>  $option
     */
    public function __construct(
        array $option
    ) {
        $this->person = $option['person'] ?? '';
        $this->text = $option['text'];
        $this->container = $option['container'] ?? null;
        $this->id = $option['id'];
        $this->optionValues = $option['option_values'] ?? [];
        $this->nextKey = $option['next_key'];
        $this->hasConditional = isset($option['has_conditional']);
        $this->hasClientCallback = isset($option['has_client_callback']);
        $this->hasReplacer = isset($option['has_replacer']);
    }

    public function setClientCallback(?string $clientCallback): void
    {
        if ($clientCallback === null) {
            return;
        }
        $this->clientCallback = $clientCallback;
    }

    /**
     * @return array<string, mixed>
     */
    public function toJson(): array
    {
        return [
            'text' => $this->text,
            'person' => $this->person,
            'container' => $this->container,
            'id' => $this->id,
            'option_values' => $this->optionValues,
            'next_key' => $this->nextKey,
            'client_callback' => $this->clientCallback,
        ];
    }
}
