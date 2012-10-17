<?php
namespace NObjects;

/**
 * Base object for model.
 *
 * @author Nesbert Hidalgo
 */
class Object
{
    // instance methods

    /**
     * Class construct if an associative array is present calls fromArray().
     *
     */
    public function __construct()
    {
        $args = func_get_args();

        // if an array or object load using fromArray
        if (!empty($args[0]) && (Validate::isAssociativeArray($args[0]) || is_object($args[0]))) {
            $this->fromArray((array)$args[0]);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJSON();
    }

    /**
     * Load model by from an associative array of key value pairs where
     * key equals property name and set{PropertyName} method exists.
     *
     * @param array $params
     * @return Object
     */
    public function fromArray(Array $params)
    {
        foreach ($params as $k => $v) {
            $method = 'set' . $k;
            // if set method exists for a key set value
            if (method_exists($this, $method)) {
                $this->{$method}($v);
            } else {
                $this->{$k} = $v;
            }
        }
        return $this;
    }

    /**
     * Return object as an associative array (property & value pair).
     *
     * @return array
     */
    public function toArray()
    {
        $class = get_class($this);

        // only reflect an object once
        static $props;
        if (empty($props[$class])) {
            $props[$class] = $this->__getAllReflectionProperties(new \ReflectionClass($this));
        }

        // adding support for public properties
        if (empty($props[$class])) {
            $props[$class] = array();
            foreach ($this as $k => $v) {
                $props[$class][] = (object)array('name' => $k, 'value' => $v);
            }
        }

        $array = array();
        foreach ($props[$class] as $rp) {

            $func = "get{$rp->name}";

            // skip properties that start with an _
            // skip functions that don't exist
            if ($rp->name{0} == '_'
                || (!method_exists($this, $func) && !isset($this->{$rp->name}))
                ) {
                continue;
            }

            // if public use temp object and don't cache
            if (isset($this->{$rp->name})) {
                $val = $rp->value;
                unset($props[$class]);
            } else {
                $val = $this->$func();
            }

            $base = __CLASS__;

            if ($val instanceof $base) {
                $array[$rp->name] = $val->toArray();
            } else if (is_array($val) || ($val instanceof \ArrayObject)) {

                if ($val instanceof \ArrayObject) {
                    $val = $val->getArrayCopy();
                }

                foreach ($val as $k => $v) {
                    if ($v instanceof $base) {
                        $val[$k] = $v->toArray();
                    } else {
                        $val[$k] = $v;
                    }
                }
                $array[$rp->name] = $val;
            } else {
                $array[$rp->name] = $val;
            }
        }

        return $array;
    }

    /**
     * @param \ReflectionClass $class
     * @return array|\ReflectionProperty[]
     */
    private function __getAllReflectionProperties(\ReflectionClass $class)
    {
        $props = $class->getProperties();

        if ($parent = $class->getParentClass()) {
            $props = array_merge($props, $this->__getAllReflectionProperties($parent));
        }

        return $props;
    }

    /**
     * Get an array of class properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return array_keys($this->toArray());
    }

    /**
     * Get an array of class property values.
     *
     * @return array
     */
    public function getPropertyValues()
    {
        return array_values($this->toArray());
    }

    /**
     * Return object as a JSON string.
     *
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    // static methods

    /**
     * Get an array of all class parents.
     *
     * @link http://us.php.net/manual/en/function.get-parent-class.php#57548
     * @param string $class
     * @param bool $reverseOrder
     * @return array
     */
    public static function ancestors($class, $reverseOrder = false)
    {
        $classes = array($class);
        while ($class = get_parent_class($class)) { $classes[] = $class; }
        array_shift($classes);
        return $reverseOrder ? array_reverse($classes) : $classes;
    }
}
