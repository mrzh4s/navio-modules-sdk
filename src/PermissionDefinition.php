<?php

namespace Navio\SDK;

/**
 * Declares a permission that this module requires.
 * Passed to ModuleRegistry::syncPermissions() which upserts them into auth.permissions.
 */
final class PermissionDefinition
{
    public bool $ui = false;

    public function __construct(
        public readonly string $name,
        public readonly string $displayName,
        public readonly string $description = '',
        public readonly string $module = '',
    ) {}

    /**
     * Fluent constructor.
     */
    public static function make(string $name, string $displayName, string $description = '', string $module = ''): self
    {
        return new self($name, $displayName, $description, $module);
    }

    /**
     * Mark this permission as needed for frontend UI gating.
     * The middleware will include it in auth.user.permissions for every page.
     * Permissions without ->ui() are enforced server-side only and never sent to the browser.
     */
    public function ui(): static
    {
        $this->ui = true;
        return $this;
    }
}
