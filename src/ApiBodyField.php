<?php

namespace Navio\SDK;

final class ApiBodyField
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $type,
        public readonly bool    $required    = false,
        public readonly string  $description = '',
        public readonly ?string $example     = null,
        public readonly array   $enum        = [],
        public readonly ?string $format      = null,
    ) {}

    public static function make(
        string  $name,
        string  $type        = 'string',
        bool    $required    = false,
        string  $description = '',
        ?string $example     = null,
        array   $enum        = [],
        ?string $format      = null,
    ): self {
        return new self(
            name:        $name,
            type:        $type,
            required:    $required,
            description: $description,
            example:     $example,
            enum:        $enum,
            format:      $format,
        );
    }

    public function toArray(): array
    {
        $data = [
            'name'        => $this->name,
            'type'        => $this->type,
            'required'    => $this->required,
            'description' => $this->description,
        ];

        if ($this->example !== null) {
            $data['example'] = $this->example;
        }

        if (!empty($this->enum)) {
            $data['enum'] = $this->enum;
        }

        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        return $data;
    }
}
