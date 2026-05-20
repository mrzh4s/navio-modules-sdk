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

    // ── i18n keys for UI labels ───────────────────────────────────────────────

    /**
     * React-Intl message ID for this module's display name.
     * When non-null, the frontend resolves this key instead of using name() directly.
     * Example: 'module.changelog.name'
     */
    public function nameI18nKey(): ?string { return null; }

    /**
     * React-Intl message ID for this module's description.
     * Example: 'module.changelog.description'
     */
    public function descriptionI18nKey(): ?string { return null; }

    /**
     * React-Intl message ID for the platform settings sidebar label.
     * Falls back to settingsLabel() / name() when null.
     * Example: 'module.changelog.settingsLabel'
     */
    public function settingsLabelI18nKey(): ?string { return null; }

    /**
     * React-Intl message ID for the workspace settings sidebar label.
     * Falls back to workspaceSettingsLabel() / name() when null.
     * Example: 'module.changelog.workspaceSettingsLabel'
     */
    public function workspaceSettingsLabelI18nKey(): ?string { return null; }

    /**
     * React-Intl message ID for this module's app sidebar section heading.
     * Falls back to menuSectionLabel() when null.
     * Example: 'module.changelog.sectionLabel'
     */
    public function menuSectionLabelI18nKey(): ?string { return null; }

    // ── Menu ──────────────────────────────────────────────────────────────────

    /** @deprecated Use menuItems() instead. */
    public function appMenuItems(): array { return []; }
    public function adminMenuItems(): array { return []; }

    /**
     * Unified app sidebar menu items. Preferred over appMenuItems().
     * Each item: ['label', 'icon', 'route', 'permission'?, 'order'?, 'children'?,
     *             'labelId'?]
     * labelId: React-Intl message ID resolved on the frontend instead of label.
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

    // ── Localisation ──────────────────────────────────────────────────────────

    /**
     * BCP-47 codes this module's UI is translated for.
     * Internal Navio modules return SupportedLanguages::primaryCodes().
     * External modules return the subset of languages they've been translated into.
     * Return [] to indicate no module-specific translation declarations.
     */
    public function supportedLanguages(): array { return []; }

    /**
     * Frontend i18n messages this module contributes to the catalog files.
     *
     * Keys use dot-notation (e.g. 'geo.createPortal'). Run `php artisan i18n:generate`
     * after changing these to rebuild the JS catalog files in resources/js/i18n/messages/.
     *
     * NOTE: Menu item label keys (the `labelId` field in adminMenuItems / appMenuItems /
     * menuItems) do NOT need to be added here — the generator automatically extracts
     * labelId→label pairs from those methods and adds them to the en-GB catalog.
     * Only add keys here for UI strings that aren't already covered by menu items.
     *
     * Format:
     * [
     *   'en-GB'   => ['geo.createPortal' => 'Create Portal', ...],
     *   'ms'      => ['geo.createPortal' => 'Cipta Portal',  ...],
     *   'zh'      => [...],
     *   'ta'      => [...],
     *   'ms-Arab' => [...],
     * ]
     *
     * Unknown locales are silently ignored by the generator.
     */
    public function i18nMessages(): array { return []; }

    // ── Feature integration points ────────────────────────────────────────────

    public function aiToolProviders(): array { return []; }
    public function searchProviders(): array { return []; }

    /**
     * Declare notification types this module can dispatch, with per-channel defaults.
     *
     * Each entry is an associative array with the following keys:
     *
     *   'type'           => string   — machine key matching the value stored in app_notifications.type
     *                                  (e.g. 'task_assigned', 'gis_approval_requested')
     *   'label'          => string   — human-readable name shown in the preferences UI
     *   'description'    => string   — one-line explanation shown as sublabel in the preferences UI
     *
     *   // Channel defaults — used to pre-seed NotificationPreference rows on first visit.
     *   // If a preference row does not exist for a user, ALL channels are considered enabled
     *   // (opt-out model). These defaults only affect the initial toggle state shown in the UI.
     *
     *   'in_app_default'  => bool    — default true  (all events appear in the in-app bell)
     *   'email_default'   => bool    — default false (high-volume types should not email by default)
     *   'push_default'    => bool    — default true  (desktop push for foreground-independent delivery)
     *   'telegram_default'=> bool    — default false
     *   'voice_default'   => bool    — default false (voice announcements for actionable events)
     *
     * Example:
     *
     *   return [
     *     [
     *       'type'             => 'task_assigned',
     *       'label'            => 'Task Assigned',
     *       'description'      => 'When a task is assigned to you.',
     *       'in_app_default'   => true,
     *       'email_default'    => true,
     *       'push_default'     => true,
     *       'telegram_default' => false,
     *       'voice_default'    => true,
     *     ],
     *   ];
     *
     * @return array<int, array{
     *   type: string,
     *   label: string,
     *   description: string,
     *   in_app_default?: bool,
     *   email_default?: bool,
     *   push_default?: bool,
     *   telegram_default?: bool,
     *   voice_default?: bool,
     * }>
     */
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

    // ── MCP (Model Context Protocol) ─────────────────────────────────────────

    /**
     * MCP-specific tools this module exposes to external AI clients.
     * Modules whose AI chat tools should also appear in MCP do NOT need to
     * override this — the platform auto-promotes aiToolProviders() to MCP.
     * Override only when you need MCP-only tools separate from AI chat tools.
     * @return McpToolDefinition[]
     */
    public function mcpTools(): array { return []; }

    /**
     * MCP resources this module exposes (browseable data URIs).
     * @return McpResourceDefinition[]
     */
    public function mcpResources(): array { return []; }

    // ── Self-declared infrastructure ──────────────────────────────────────────

    public function routeFiles(): array       { return []; }
    public function apiRouteFiles(): array    { return []; }
    public function publicRouteFiles(): array { return []; }
    public function portBindings(): array     { return []; }
    public function singletons(): array       { return []; }
    public function eventListeners(): array   { return []; }

    /**
     * Auto-detects Database/Migrations/ adjacent to the module class file.
     * Override to specify custom paths or return [] to disable.
     */
    public function migrationPaths(): array
    {
        $dir = dirname((new \ReflectionClass(static::class))->getFileName())
            . '/Database/Migrations';

        return is_dir($dir) ? [$dir] : [];
    }

    /**
     * FQN of this module's seeder class, or null if no seeding is needed.
     * Convention: Database/Seeders/{ModuleName}Seeder.php adjacent to the module file.
     * Each module declares its seeder explicitly — auto-discovery is intentionally
     * skipped here because class-loading order is not guaranteed.
     */
    public function seeder(): ?string { return null; }
}
