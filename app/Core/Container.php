<?php
namespace App\Core;

class Container
{
    private array $instances = [];

    /**
     * Enregistrer une définition
     */
    public function set(string $id, callable $factory): void
    {
        $this->instances[$id] = $factory;
    }

    /**
     * Récupérer une instance
     */
    public function get(string $id): object
    {
        // Si une définition existe
        if (isset($this->instances[$id])) {
            // Si c'est une closure, on l'exécute
            if (is_callable($this->instances[$id])) {
                $instance = $this->instances[$id]($this);
                $this->instances[$id] = $instance;
                return $instance;
            }
            // Sinon, on retourne l'objet déjà instancié
            return $this->instances[$id];
        }

        // Résolution automatique par réflexion
        return $this->resolve($id);
    }

    /**
     * Résoudre une classe via la réflexion
     */
    private function resolve(string $className): object
    {
        $reflection = new \ReflectionClass($className);

        if (!$reflection->isInstantiable()) {
            throw new \Exception("La classe '$className' n'est pas instanciable.");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }
                throw new \Exception("Le paramètre '{$parameter->getName()}' n'a pas de type hint.");
            }

            $typeName = $type->getName();

            if ($type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Impossible de résoudre le paramètre '{$parameter->getName()}' (type $typeName).");
                }
            } else {
                // Résoudre la dépendance récursivement
                $dependencies[] = $this->get($typeName);
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}