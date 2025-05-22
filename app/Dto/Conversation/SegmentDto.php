<?php

namespace App\Dto\Conversation;

final class SegmentDto
{
    public readonly string $index;

    /**
     * @var \App\Dto\Conversation\OptionDto[]
     */
    public array $options;

    public readonly ?string $header;

    public readonly bool $hasServerEvent;

    public readonly bool $hasClientEvent;

    /**
     * @var string[]
     */
    public readonly array $server_event_results;

    /**
     * @param  array<string, mixed>  $segment
     */
    public function __construct(
        array $segment
    ) {
        $this->index = $segment['index'];
        $this->header = $segment['header'] ?? null;
        $this->options = array_map(
            fn ($option) => new OptionDto($option),
            $segment['options'] ?? []
        );
        $this->hasServerEvent = isset($segment['has_server_event']);
        $this->hasClientEvent = isset($segment['has_client_event']);
        $this->server_event_results = $segment['server_event_results'] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'header' => $this->header,
            'index' => $this->index,
            'options' => array_map(
                fn ($option) => $option->toJson(),
                $this->options
            ),
        ];
    }
}
