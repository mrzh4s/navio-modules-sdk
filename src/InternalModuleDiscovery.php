<?php

namespace Navio\SDK;

class InternalModuleDiscovery
{
    private static ?array $cachedModules = null;

    /**
     * Scan Features/{X}/{Y}Module.php, instantiate all concrete ModuleDefinitionContract
     * implementations, sort by order() ascending, and cache for the request lifetime.
     *
     * Uses get_declared_classes() diff to detect the class introduced by each require_once.
     * Eloquent models ending in "Module" at depth 3+ are excluded structurally by the glob.
     */
    public static function scan(string $featuresPath): array
    {
        if (static::$cachedModules !== null) {
            return static::$cachedModules;
        }

        $modules = [];
        foreach (glob(rtrim($featuresPath, '/') . '/*/*Module.php') as $file) {
            $before = get_declared_classes();
            require_once $file;
            $after = get_declared_classes();

            foreach (array_diff($after, $before) as $class) {
                if (
                    is_subclass_of($class, ModuleDefinitionContract::class)
                    && !(new \ReflectionClass($class))->isAbstract()
                ) {
                    $modules[] = new $class();
                }
            }
        }

        usort($modules, fn ($a, $b) => $a->order() <=> $b->order());
        static::$cachedModules = $modules;

        return $modules;
    }

    public static function flush(): void
    {
        static::$cachedModules = null;
    }
}
