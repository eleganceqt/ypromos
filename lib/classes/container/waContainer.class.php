<?php

require __DIR__ . '/../../vendors/vendor/autoload.php';

use Psr\Container\ContainerInterface;

class waContainer implements ContainerInterface
{
    protected static $instance;

    protected $resolved = [];

    protected $appAliases = [
        'core' => 'wa-system'
    ];

    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {

    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {

    }

    public function make($appId, $classId)
    {
        return $this->resolve($appId, $classId);
    }

    public function makeWith($appId, $classId, $parameters = [])
    {
        return $this->resolve($appId, $classId, $parameters);
    }


    protected function resolve($appId, $classId, $parameters = [])
    {
        if ($this->hasAlias($appId)) {
            $appId = $this->getAlias($appId);
        }

        if ($this->wasSolved($appId, $classId)) {
            return $this->resolved[$appId][$classId];
        }

        $this->bootApp($appId);

        if ($this->distinguishPlugin($classId)) {
            $object = $this->buildPlugin($appId, $this->extractPluginId($appId, $classId));
        } else {
            $object = $this->buildClass($classId, $parameters);
        }


//        if (! $this->hasParameters($parameters)) {
//            $this->registerSolved($appId, $classId, $object);
//        }

        return $object;
    }

    protected function registerSolved($appId, $classId, $object)
    {
        $this->resolved[$appId][$classId] = $object;
    }

    protected function wasSolved($appId, $classId)
    {
        return isset($this->resolved[$appId][$classId]);
    }


    protected function hasParameters($parameters)
    {
        return is_array($parameters) && ! empty($parameters);
    }


    protected function distinguishPlugin($classId)
    {
        return substr(strtolower($classId), -6) === 'plugin';
    }

    private function extractPluginId($appId, $classId)
    {
        return str_replace($appId, '', str_replace('plugin', '', strtolower($classId)));
    }


    protected function buildPlugin($appId, $pluginId)
    {
        return wa($appId)->getPlugin($pluginId);
    }

    protected function buildClass($classId, $parameters = [])
    {
        $reflector = new ReflectionClass($classId);

        if (! $reflector->isInstantiable()) {
            throw new waEntryNotInstantiableException('Target is not instantiable.');
        }

        return $reflector->newInstanceArgs($parameters);
    }

    protected function bootApp($appId)
    {
        wa($appId);
    }

    protected function hasAlias($appId)
    {
        return isset($this->appAliases[$appId]);
    }

    protected function getAlias($appId)
    {
        return $this->appAliases[$appId];
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

}