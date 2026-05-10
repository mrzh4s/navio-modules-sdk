<?php

namespace Orbistra\SDK;

interface ModuleDefinitionContract
{
    // ── Identity ──────────────────────────────────────────────────────────────

    public function slug(): string;
    public function name(): string;
    public function description(): string;
    public function icon(): string;
    public function version(): string;
    public function order(): int;
    public function isCore(): bool;

    // ── Author / marketplace metadata ─────────────────────────────────────────

    public function author(): string;
    public function authorUrl(): string;
    public function homepage(): string;
    public function composerPackage(): string;

    // ── Settings integration ──────────────────────────────────────────────────

    public function hasSettings(): bool;
    public function settingsRoute(): ?string;
    public function settingsLabel(): string;
    public function settingsIcon(): string;

    /**
     * Sub-links shown beneath this module in the Settings sidebar.
     * Each item: ['label' => string, 'route' => string, 'icon' => string, 'permission' => string|null]
     */
    public function settingsSubLinks(): array;

    // ── Workspace settings ────────────────────────────────────────────────────

    /**
     * Route name for this module's per-workspace settings page.
     * Route must accept {workspace} parameter (UUID).
     * Null = module has no workspace-level settings.
     */
    public function workspaceSettingsRoute(): ?string;

    /** Label shown in the workspace settings sidebar for this module. */
    public function workspaceSettingsLabel(): string;

    /** Tabler icon name shown in the workspace settings sidebar. */
    public function workspaceSettingsIcon(): string;

    // ── Menu registration ─────────────────────────────────────────────────────

    /** @deprecated Use menuItems() instead. */
    public function appMenuItems(): array;

    /**
     * Admin panel sidebar menu items.
     * Shape per item: ['label', 'icon', 'route', 'permission'?, 'order'?, 'is_heading'?,
     *                  'default_roles'?, 'children'?]
     *
     * default_roles: workspace role slugs that see this item by default.
     *   []             = visible to all roles (default)
     *   ['owner','admin'] = only owners/admins see it unless workspace overrides
     */
    public function adminMenuItems(): array;

    /** Unified app sidebar menu items. Preferred over appMenuItems(). */
    public function menuItems(): array;

    /** Section heading label for app sidebar. Null = no heading. */
    public function menuSectionLabel(): ?string;

    /** Section heading label for admin sidebar. Defaults to menuSectionLabel(). */
    public function adminMenuSectionLabel(): ?string;

    // ── Permissions ───────────────────────────────────────────────────────────

    /**
     * Returns an array of PermissionDefinition objects this module needs.
     * These are upserted by ModuleRegistry::syncPermissions().
     */
    public function permissions(): array;

    // ── Database ──────────────────────────────────────────────────────────────

    /**
     * PostgreSQL schema name for this module's tables.
     * Null = module uses an existing schema or manages its own.
     */
    public function schemaName(): ?string;

    // ── Dependencies ──────────────────────────────────────────────────────────

    /**
     * Slugs of modules that must be enabled before this module can be enabled.
     * The ToggleModuleHandler enforces this and cascade-disables children when
     * a parent is disabled.
     */
    public function requires(): array;

    /**
     * Whether this module requires a valid tenant license to activate.
     */
    public function requiresLicense(): bool;

    /**
     * Product / feature identifier sent to the licensing server.
     * Only meaningful when requiresLicense() returns true.
     */
    public function licenseProductId(): string;

    // ── Lifecycle hooks ───────────────────────────────────────────────────────

    /**
     * Called when this module is enabled via the modules toggle.
     */
    public function onEnabled(): void;

    /**
     * Called when this module is disabled via the modules toggle.
     */
    public function onDisabled(): void;

    /**
     * Called when a version change is detected during boot or modules:sync.
     * Use to run data migrations or initialisation logic for the new version.
     */
    public function onUpgrade(string $fromVersion, string $toVersion): void;

    // ── Feature integration points ────────────────────────────────────────────

    /**
     * FQN class names of AI tool providers (must implement ToolProviderInterface).
     */
    public function aiToolProviders(): array;

    /**
     * FQN class names of search providers (must implement SearchProviderInterface).
     */
    public function searchProviders(): array;

    /**
     * Notification types this module can emit.
     * Each item: ['type' => string, 'label' => string, 'description' => string]
     */
    public function notificationTypes(): array;

    /**
     * Dashboard widget definitions provided by this module.
     * Each item: ['key' => string, 'label' => string, 'description' => string, 'component' => string|null]
     */
    public function widgetProviders(): array;

    /**
     * Queue names this module uses (for monitoring and cleanup).
     */
    public function queueNames(): array;

    /**
     * Scheduled tasks declared by this module.
     * Each item: ['command' => string, 'cron' => string, 'description' => string]
     */
    public function scheduledTasks(): array;

    // ── GraphQL integration points ────────────────────────────────────────────

    /**
     * Whether this module exposes GraphQL types, queries, or mutations.
     * Schema loading is handled by the ServiceProvider via graphqlSchemaFiles().
     */
    public function exposesGraphQL(): bool;

    /**
     * Human-readable list of GraphQL types this module contributes.
     * Example: ['Project', 'ProjectTask', 'ProjectMember']
     */
    public function graphqlTypes(): array;

    // ── API operation declaration ─────────────────────────────────────────────

    /**
     * REST API operations this module exposes.
     * @return ApiOperationDefinition[]
     */
    public function restOperations(): array;

    /**
     * GraphQL queries and mutations this module exposes.
     * @return GraphQLOperationDefinition[]
     */
    public function graphqlOperations(): array;

    // ── Self-declared infrastructure ──────────────────────────────────────────

    /**
     * Absolute paths to web/admin route files this module owns.
     * Loaded in module order() sequence by bootstrap/app.php.
     * @return string[]
     */
    public function routeFiles(): array;

    /**
     * Absolute paths to REST API route files (/api/v1/... prefix assumed by caller).
     * @return string[]
     */
    public function apiRouteFiles(): array;

    /**
     * Absolute paths to public catch-all route files — loaded LAST after all other routes.
     * Only WebPublishingModule uses this (URL slug catch-all).
     * @return string[]
     */
    public function publicRouteFiles(): array;

    /**
     * Port interface → concrete adapter class bindings.
     * Registered in ServiceProvider::register() phase (before boot).
     * @return array<class-string, class-string>
     */
    public function portBindings(): array;

    /**
     * Singleton bindings (abstract → concrete class-string or Closure).
     * Registered in ServiceProvider::register() phase.
     * @return array<class-string, class-string|\Closure>
     */
    public function singletons(): array;

    /**
     * Absolute paths to migration directories owned by this module.
     * Loaded via loadMigrationsFrom() in AppServiceProvider::boot().
     * @return string[]
     */
    public function migrationPaths(): array;

    /**
     * Event class → listener class array mappings.
     * Registered via the event dispatcher when the module is registered in ModuleRegistry.
     * @return array<class-string, class-string[]>
     */
    public function eventListeners(): array;

    /**
     * FQN of this module's seeder class, or null if no seeding is needed.
     * Called by DatabaseSeeder and the modules:seed artisan command.
     */
    public function seeder(): ?string;
}
