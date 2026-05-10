<?php

namespace Navio\SDK;

final class GraphQLOperationDefinition
{
    public function __construct(
        public readonly string  $type,
        public readonly string  $name,
        public readonly string  $summary,
        public readonly string  $description    = '',
        public readonly ?string $scope          = null,
        public readonly bool    $requiresAuth   = true,
        public readonly bool    $isPublic       = false,
        public readonly bool    $superAdminOnly = false,
        public readonly array   $args           = [],
        public readonly string  $returns        = '',
    ) {}

    public static function make(
        string $type,
        string $name,
        string $summary,
        string $description    = '',
        ?string $scope         = null,
        bool $requiresAuth     = true,
        bool $isPublic         = false,
        bool $superAdminOnly   = false,
        array $args            = [],
        string $returns        = '',
    ): self {
        return new self(
            type:           $type,
            name:           $name,
            summary:        $summary,
            description:    $description,
            scope:          $scope,
            requiresAuth:   $requiresAuth,
            isPublic:       $isPublic,
            superAdminOnly: $superAdminOnly,
            args:           $args,
            returns:        $returns,
        );
    }

    public function toArray(): array
    {
        return [
            'type'             => $this->type,
            'name'             => $this->name,
            'summary'          => $this->summary,
            'description'      => $this->description,
            'scope'            => $this->scope,
            'requires_auth'    => $this->requiresAuth,
            'is_public'        => $this->isPublic,
            'super_admin_only' => $this->superAdminOnly,
            'args'             => $this->args,
            'returns'          => $this->returns,
        ];
    }
}
