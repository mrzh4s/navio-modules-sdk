<?php

namespace Orbistra\SDK;

use Illuminate\Support\ServiceProvider;

/**
 * Base ServiceProvider for external Orbistra modules.
 *
 * Vendors extend this and implement moduleClass(). Everything else is optional.
 *
 * Usage in vendor's composer.json:
 *   "extra": { "laravel": { "providers": ["Vendor\\MyModule\\MyServiceProvider"] } }
 *
 * Example:
 *   class CrmServiceProvider extends ModuleServiceProvider
 *   {
 *       protected function moduleClass(): string { return CrmModule::class; }
 *
 *       protected function routeFiles(): array {
 *           return [__DIR__.'/../routes/crm.php'];
 *       }
 *
 *       protected function migrationPaths(): array {
 *           return [__DIR__.'/../database/migrations'];
 *       }
 *   }
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The module definition class to register.
     * Must be a class implementing ModuleDefinitionContract.
     */
    abstract protected function moduleClass(): string;

    /**
     * Absolute paths to route files to load (web + API mixed, existing pattern).
     */
    protected function routeFiles(): array { return []; }

    /**
     * Absolute paths to dedicated REST API route files.
     * Semantically separate from routeFiles() — these should only contain
     * API routes grouped under ['api', 'auth:api'] middleware.
     */
    protected function apiRouteFiles(): array { return []; }

    /**
     * Absolute paths to GraphQL SDL schema files (.graphql).
     * Registered with the platform's GraphQLSchemaRegistry at boot time.
     * Use `extend type Query` / `extend type Mutation` in these files.
     */
    protected function graphqlSchemaFiles(): array { return []; }

    /**
     * Absolute paths to migration directories to load.
     */
    protected function migrationPaths(): array { return []; }

    /**
     * Interface → implementation bindings (Ports & Adapters).
     * Bound in register() so they are available before boot().
     *
     * @return array<class-string, class-string>
     */
    protected function portBindings(): array { return []; }

    public function register(): void
    {
        foreach ($this->portBindings() as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    public function boot(): void
    {
        // Load migrations via standard Laravel package API
        foreach ($this->migrationPaths() as $path) {
            $this->loadMigrationsFrom($path);
        }

        // Register module with ModuleRegistry.
        // callAfterResolving handles boot order: if the registry singleton already
        // exists (AppServiceProvider ran first), the callback fires immediately.
        // If not, it is queued and fires when the singleton is first resolved.
        $this->callAfterResolving(
            \App\Core\Modules\ModuleRegistry::class,
            function ($registry) {
                $moduleClass = $this->moduleClass();
                $registry->register(new $moduleClass());
            }
        );

        // Load routes after application is fully bootstrapped
        $this->booted(function () {
            foreach ($this->routeFiles() as $file) {
                require $file;
            }
            foreach ($this->apiRouteFiles() as $file) {
                require $file;
            }
        });

        // Register GraphQL schema files with the platform registry (if present)
        $this->callAfterResolving(
            \App\Core\GraphQL\GraphQLSchemaRegistry::class,
            function ($registry) {
                foreach ($this->graphqlSchemaFiles() as $file) {
                    $registry->registerSchemaFile($file);
                }
            }
        );
    }
}
