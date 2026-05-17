<?php

namespace Navio\SDK;

final class LanguageDefinition
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $nativeName,
        public readonly string $direction,
        public readonly string $flag = '',
    ) {}

    public static function make(
        string $code,
        string $name,
        string $nativeName,
        string $direction,
        string $flag = '',
    ): self {
        return new self($code, $name, $nativeName, $direction, $flag);
    }

    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }

    public function toArray(): array
    {
        return [
            'code'       => $this->code,
            'name'       => $this->name,
            'nativeName' => $this->nativeName,
            'direction'  => $this->direction,
            'flag'       => $this->flag,
        ];
    }
}
