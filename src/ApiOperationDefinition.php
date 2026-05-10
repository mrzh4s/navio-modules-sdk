<?php

namespace Navio\SDK;

final class ApiOperationDefinition
{
    public function __construct(
        public readonly string  $method,
        public readonly string  $path,
        public readonly string  $summary,
        public readonly string  $description    = '',
        public readonly ?string $scope          = null,
        public readonly bool    $requiresAuth   = true,
        public readonly bool    $isPublic       = false,
        public readonly bool    $superAdminOnly = false,
        public readonly array   $parameters     = [],
        public readonly array   $requestBody    = [],
        public readonly array   $responses      = [],
    ) {}

    public static function make(
        string $method,
        string $path,
        string $summary,
        string $description    = '',
        ?string $scope         = null,
        bool $requiresAuth     = true,
        bool $isPublic         = false,
        bool $superAdminOnly   = false,
        array $parameters      = [],
        array $requestBody     = [],
        array $responses       = [],
    ): self {
        return new self(
            method:          $method,
            path:            $path,
            summary:         $summary,
            description:     $description,
            scope:           $scope,
            requiresAuth:    $requiresAuth,
            isPublic:        $isPublic,
            superAdminOnly:  $superAdminOnly,
            parameters:      $parameters,
            requestBody:     $requestBody,
            responses:       $responses,
        );
    }

    public function toArray(): array
    {
        return [
            'method'           => $this->method,
            'path'             => $this->path,
            'summary'          => $this->summary,
            'description'      => $this->description,
            'scope'            => $this->scope,
            'requires_auth'    => $this->requiresAuth,
            'is_public'        => $this->isPublic,
            'super_admin_only' => $this->superAdminOnly,
            'parameters'       => $this->parameters,
            'request_body'     => $this->requestBody,
            'responses'        => $this->responses,
        ];
    }
}
