<?php

namespace Navio\SDK;

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
     * Each item: ['label' => string, 'route' => string, 'icon' => string,
     *             'permission' => string|null, 'labelId'? => string]
     * labelId: React-Intl message ID resolved on the frontend instead of label.
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

    // ── i18n keys for UI labels ───────────────────────────────────────────────

    /** React-Intl message ID for this module's display name. Null = use name(). */
    public function nameI18nKey(): ?string;

    /** React-Intl message ID for this module's description. Null = use description(). */
    public function descriptionI18nKey(): ?string;

    /** React-Intl message ID for the platform settings sidebar label. Null = use settingsLabel(). */
    public function settingsLabelI18nKey(): ?string;

    /** React-Intl message ID for the workspace settings sidebar label. Null = use workspaceSettingsLabel(). */
    public function workspaceSettingsLabelI18nKey(): ?string;

    /** React-Intl message ID for the app sidebar section heading. Null = use menuSectionLabel(). */
    public function menuSectionLabelI18nKey(): ?string;

    // ── Menu registration ─────────────────────────────────────────────────────

    /** @deprecated Use menuItems() instead. */
    public function appMenuItems(): array;

    /**
     * Admin panel sidebar menu items.
     * Shape per item: ['label', 'icon', 'route', 'permission'?, 'order'?, 'is_heading'?,
     *                  'default_roles'?, 'children'?, 'labelId'?]
     *
     * labelId: React-Intl message ID resolved on the frontend instead of label.
     * default_roles: workspace role slugs that see this item by default.
     *   []             = visible to all roles (default)
     *   ['owner','admin'] = only owners/admins see it unless workspace overrides
     */
    public function adminMenuItems(): array;

    /**
     * Unified app sidebar menu items. Preferred over appMenuItems().
     * Shape per item: ['label', 'icon', 'route', 'permission'?, 'order'?, 'children'?, 'labelId'?]
     * labelId: React-Intl message ID resolved on the frontend instead of label.
     */
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

    // ── Mobile navigation ─────────────────────────────────────────────────────

    /**
     * Bottom nav items shown when the user is inside this module's context.
     * Return [] to inherit the workspace-global (user-pinned) bottom nav.
     *
     * Each item shape:
     *   ['label' => string, 'labelId' => string|null, 'icon' => string,
     *    'route' => string|null, 'is_fab' => bool, 'fab_icon' => string|null,
     *    'children' => array]
     */
    public function mobileNavItems(): array;

    /**
     * Visual variant for this module's bottom nav.
     * 'standard' | 'cta-center' | 'material3'
     */
    public function mobileNavVariant(): string;

    /**
     * Number of items to place LEFT of the CTA in 'cta-center' variant.
     * null = auto (floor(count/2)).
     */
    public function mobileNavCtaSplit(): ?int;

    /**
     * URL prefixes that activate this module's bottom nav.
     * useModuleNav() on the frontend matches the current URL against these prefixes.
     * Example: ['/projects', '/sprints']
     * @return string[]
     */
    public function mobileNavUrlPrefixes(): array;

    /**
     * Mobile header action buttons shown when the user is on this module's pages.
     * Max 2 shown inline; extras collapse into a kebab overflow sheet.
     *
     * Each item shape (same as toolbar menu items):
     *   ['label' => string, 'icon' => string, 'route' => string|null,
     *    'action' => string|null, 'permission' => string|null]
     */
    public function mobileHeaderActions(): array;

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

    // ── MCP (Model Context Protocol) ─────────────────────────────────────────

    /** @return McpToolDefinition[] */
    public function mcpTools(): array;

    /** @return McpResourceDefinition[] */
    public function mcpResources(): array;

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
     * Default auto-detects Database/Migrations/ adjacent to the module class file.
     * Override to specify custom paths or return [] to disable auto-detection.
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
     * Called by DatabaseSeeder during `php artisan db:seed`.
     * Convention: Database/Seeders/{ModuleName}Seeder.php adjacent to the module class file.
     */
    public function seeder(): ?string;

    /**
     * BCP-47 codes this module's UI is translated for.
     * Internal modules return SupportedLanguages::primaryCodes().
     * External modules return the subset of languages they support.
     * Return [] to indicate no module-specific translation declarations.
     */
    public function supportedLanguages(): array;

    /**
     * Frontend i18n messages this module contributes to the catalog files.
     * Run `php artisan i18n:generate` to rebuild JS catalogs from all module sources.
     *
     * @return array<string, array<string, string>> locale → [messageId => translation]
     */
    public function i18nMessages(): array;
}
