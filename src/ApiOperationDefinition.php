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
        public readonly array   $bodyFields     = [],
        public readonly string  $bodyDescription = '',
    ) {}

    public static function make(
        string  $method,
        string  $path,
        string  $summary,
        string  $description     = '',
        ?string $scope           = null,
        bool    $requiresAuth    = true,
        bool    $isPublic        = false,
        bool    $superAdminOnly  = false,
        array   $parameters      = [],
        array   $requestBody     = [],
        array   $responses       = [],
        array   $bodyFields      = [],
        string  $bodyDescription = '',
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
            bodyFields:      $bodyFields,
            bodyDescription: $bodyDescription,
        );
    }

    public function toArray(): array
    {
        // If bodyFields are provided, they take precedence over the raw requestBody array.
        $resolvedBody = [];
        if (!empty($this->bodyFields)) {
            $resolvedBody = [
                'description' => $this->bodyDescription,
                'properties'  => array_map(fn (ApiBodyField $f) => $f->toArray(), $this->bodyFields),
            ];
        } elseif (!empty($this->requestBody)) {
            $resolvedBody = $this->requestBody;
        }

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
            'request_body'     => $resolvedBody,
            'responses'        => $this->responses,
        ];
    }
}
