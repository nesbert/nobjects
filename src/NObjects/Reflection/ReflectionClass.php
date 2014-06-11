<?php

namespace NObjects\Reflection;

/**
 * ReflectionClass enhances the base PHP ReflectionClass by caching the output of certain methods
 */
class ReflectionClass extends \ReflectionClass
{
    /**
     * Cache of class properties with inherited
     *
     * @var array
     */
    private static $ancestorPrivProps = array();

    /**
     * Cache of class properties
     *
     * @var array
     */
    private static $localProps = array();

    /**
     * Gets parent class. Returns false if no parent class exists.
     *
     * @return ReflectionClass|boolean
     */
    public function getParentClass()
    {
        if ($parentReflClass = parent::getParentClass()) {
            return new static($parentReflClass->getName());
        }

        return false;
    }

    /**
     * Gets the class's properties with local caching.
     *
     * @param integer|null $filter
     *
     * @return \ReflectionProperty[]
     */
    public function getProperties($filter = null)
    {
        $cacheIndex = is_null($filter) ? 'nofilter' : $filter;
        if (empty(self::$localProps[$this->getName()])) {
            self::$localProps[$this->getName()] = array();
        }
        if (empty(self::$localProps[$this->getName()][$cacheIndex])) {
            // PHP has a bug with the default parameter of getProperties()... if the value null is passed,
            // it doesn't treat it the same as if no value has been passed as the parameter.
            if (is_null($filter)) {
                $props = parent::getProperties();
            } else {
                $props = parent::getProperties($filter);
            }
            self::$localProps[$this->getName()][$cacheIndex] = $props;
        }

        return self::$localProps[$this->getName()][$cacheIndex];
    }

    /**
     * Returns an array of private properties that exist in parent classes. A private property name will not be
     * included more than once.
     *
     * @return array
     */
    public function getAncestorPrivateProperties()
    {
        if (empty(self::$ancestorPrivProps[$this->getName()])) {
            self::$ancestorPrivProps[$this->getName()] = array();
        }

        if ($parent = $this->getParentClass()) {
            $collected = array();
            $this->recurGetPrivateProps(self::$ancestorPrivProps[$this->getName()], $collected, $parent);
        };

        return self::$ancestorPrivProps[$this->getName()];
    }

    /**
     * Recursive method to collect properties from parent classes
     *
     * @param array           $props
     * @param array           $collectedNames
     * @param ReflectionClass $refl
     */
    private function recurGetPrivateProps(array &$props, array &$collectedNames, ReflectionClass $refl)
    {
        $collProps = $refl->getProperties(\ReflectionProperty::IS_PRIVATE);

        $notCollectedProps = array_filter($collProps, function ($elem) use ($collectedNames) {
            /** @var $elem \ReflectionProperty */
            return !in_array($elem->name, $collectedNames);
        });

        $newCollectedProps = array_map(function ($elem) {
            /** @var $elem \ReflectionProperty */
            return $elem->name;
        }, $notCollectedProps);

        $collectedNames = array_merge($collectedNames, $newCollectedProps);

        $props = array_merge($props, $notCollectedProps);
        if ($parent = $refl->getParentClass()) {
            $this->recurGetPrivateProps($props, $collectedNames, $parent);
        }
    }
}
