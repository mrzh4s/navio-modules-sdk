<?php

namespace Navio\SDK;

final class McpResourceDefinition
{
    public function __construct(
        public readonly string $uri,
        public readonly string $name,
        public readonly string $description = '',
        public readonly string $mimeType    = 'application/json',
    ) {}

    public static function make(
        string $uri,
        string $name,
        string $description = '',
        string $mimeType    = 'application/json',
    ): self {
        return new self(
            uri:         $uri,
            name:        $name,
            description: $description,
            mimeType:    $mimeType,
        );
    }

    public function toArray(): array
    {
        return [
            'uri'         => $this->uri,
            'name'        => $this->name,
            'description' => $this->description,
            'mimeType'    => $this->mimeType,
        ];
    }
}
