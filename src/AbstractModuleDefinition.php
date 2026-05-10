<?php

namespace Navio\SDK;

/**
 * Default implementations for ModuleDefinitionContract.
 * External vendors extend this class and override only what they need.
 * Internal Navio modules extend app/Core/Modules/ModuleDefinition.php which
 * also extends this class to stay in sync.
 */
abstract class AbstractModuleDefinition implements ModuleDefinitionContract
{
    // ── Identity (must override) ──────────────────────────────────────────────

    abstract public function slug(): string;
    abstract public function name(): string;

    // ── Identity (optional override) ─────────────────────────────────────────

    public function description(): string { return ''; }
    public function icon(): string { return 'IconBox'; }
    public function version(): string { return '1.0.0'; }
    public function order(): int { return 100; }
    public function isCore(): bool { return false; }

    // ── Author / marketplace metadata ─────────────────────────────────────────

    public function author(): string { return ''; }
    public function authorUrl(): string { return ''; }
    public function homepage(): string { return ''; }
    public function composerPackage(): string { return ''; }

    // ── Settings ──────────────────────────────────────────────────────────────

    public function hasSettings(): bool { return false; }
    public function settingsRoute(): ?string { return null; }
    public function settingsLabel(): string { return $this->name(); }
    public function settingsIcon(): string { return 'IconSettings'; }
    public function settingsSubLinks(): array { return []; }

    // ── Workspace settings ────────────────────────────────────────────────────

    /** Route name for this module's per-workspace settings page. Null = no workspace settings. */
    public function workspaceSettingsRoute(): ?string { return null; }
    public function workspaceSettingsLabel(): string  { return $this->name(); }
    public function workspaceSettingsIcon(): string   { return 'IconSettings'; }

    // ── Menu ──────────────────────────────────────────────────────────────────

    /** @deprecated Use menuItems() instead. */
    public function appMenuItems(): array { return []; }
    public function adminMenuItems(): array { return []; }

    /**
     * Unified app sidebar menu items. Preferred over appMenuItems().
     * Each item: ['label', 'icon', 'route', 'permission'?, 'order'?, 'children'?]
     */
    public function menuItems(): array { return []; }

    /**
     * Section heading label shown above this module's app sidebar items.
     * Return null for no heading.
     */
    public function menuSectionLabel(): ?string { return null; }

    /**
     * Section heading label for the admin sidebar. Defaults to menuSectionLabel().
     * Override and return null to suppress admin headings for this module.
     */
    public function adminMenuSectionLabel(): ?string { return $this->menuSectionLabel(); }

    // ── Permissions ───────────────────────────────────────────────────────────

    /** @return PermissionDefinition[] */
    public function permissions(): array { return []; }

    // ── Database ──────────────────────────────────────────────────────────────

    public function schemaName(): ?string { return null; }

    // ── Dependencies ──────────────────────────────────────────────────────────

    public function requires(): array { return []; }
    public function requiresLicense(): bool { return false; }
    public function licenseProductId(): string { return ''; }

    // ── Lifecycle hooks ───────────────────────────────────────────────────────

    public function onEnabled(): void {}
    public function onDisabled(): void {}
    public function onUpgrade(string $fromVersion, string $toVersion): void {}

    // ── Feature integration points ────────────────────────────────────────────

    public function aiToolProviders(): array { return []; }
    public function searchProviders(): array { return []; }
    public function notificationTypes(): array { return []; }
    public function widgetProviders(): array { return []; }
    public function queueNames(): array { return []; }
    public function scheduledTasks(): array { return []; }

    // ── GraphQL integration points ────────────────────────────────────────────

    /**
     * Whether this module exposes GraphQL types, queries, or mutations.
     * Schema loading is handled by the ServiceProvider via graphqlSchemaFiles().
     * This is informational — used by the API discovery endpoint.
     */
    public function exposesGraphQL(): bool { return false; }

    /**
     * Human-readable list of GraphQL types this module contributes.
     * Informational only — used in the developer API map endpoint.
     * Example: ['Project', 'ProjectTask', 'ProjectMember']
     */
    public function graphqlTypes(): array { return []; }

    // ── API operation declaration ─────────────────────────────────────────────

    /**
     * REST API operations this module exposes.
     * Used to generate interactive documentation in the developer portal.
     * @return ApiOperationDefinition[]
     */
    public function restOperations(): array { return []; }

    /**
     * GraphQL queries and mutations this module exposes.
     * Used to populate the GraphQL operation explorer in the developer portal.
     * @return GraphQLOperationDefinition[]
     */
    public function graphqlOperations(): array { return []; }

    // ── Self-declared infrastructure ──────────────────────────────────────────

    public function routeFiles(): array       { return []; }
    public function apiRouteFiles(): array    { return []; }
    public function publicRouteFiles(): array { return []; }
    public function portBindings(): array     { return []; }
    public function singletons(): array       { return []; }
    public function migrationPaths(): array   { return []; }
    public function eventListeners(): array   { return []; }
    public function seeder(): ?string         { return null; }
}
