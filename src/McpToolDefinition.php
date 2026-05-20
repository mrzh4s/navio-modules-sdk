<?php

namespace Navio\SDK;

final class McpToolDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array  $inputSchema,
        public readonly array  $annotations = [],
    ) {}

    public static function make(
        string $name,
        string $description,
        array  $inputSchema,
        array  $annotations = [],
    ): self {
        return new self(
            name:        $name,
            description: $description,
            inputSchema: $inputSchema,
            annotations: $annotations,
        );
    }

    public function toArray(): array
    {
        $data = [
            'name'        => $this->name,
            'description' => $this->description,
            'inputSchema' => $this->inputSchema,
        ];

        if (!empty($this->annotations)) {
            $data['annotations'] = $this->annotations;
        }

        return $data;
    }
}
