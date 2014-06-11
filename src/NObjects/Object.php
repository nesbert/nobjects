<?php
namespace NObjects;

use NObjects\Reflection\ReflectionClass;

/**
 * Base object for model.
 *
 * @author Nesbert Hidalgo
 */
class Object
{
    // instance methods

    /**
     * Public constructor
     * If an associative array is passed as an argument, hydrate object using calls fromArray().
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
            } else if (!empty($k)) {
                $this->{$k} = $v;
            }
        }
        return $this;
    }

    /**
     * Return object as an associative array (property & value pair).
     *
     * @param callable $valueClosure
     * @return array
     */
    public function toArray($valueClosure = null)
    {
        $refl = new ReflectionClass($this);

        // Collect properties from this class as well as private property names from parents
        // which may have accessor methods.
        $props = array_unique(array_merge($refl->getProperties(), $refl->getAncestorPrivateProperties()));

        // adding support for public properties
        foreach ($this as $k => $v) {
            $props[] = (object)array('name' => $k, 'value' => $v);
        }

        $array = array();
        foreach ($props as $rp) {

            $func = $this->_getAccessorMethod($rp->name);

            // skip properties that start with an _
            // skip functions that don't exist
            if ($rp->name{0} == '_'
                || (!method_exists($this, $func) && !isset($this->{$rp->name}))
            ) {
                continue;
            }

            // if public use temp object and don't cache
            if ($rp instanceof \ReflectionProperty) {
                if ($rp->isPublic()) {
                    $val = $rp->getValue($this);
                } else {
                    $val = $this->$func();
                }
            } else {
                $val = $rp->value;
            }

            $base = __CLASS__;

            switch (true) {
                // is NObjects\Object instance
                case $val instanceof $base:
                    $array[$rp->name] = $val->toArray($valueClosure);
                    break;
                // intercept array types (ArrayObject, Collection) and convert $val to array
                case method_exists($val, 'toArray'):
                    $val = $val->toArray();
                    break;
                case $val instanceof \ArrayObject:
                    $val = $val->getArrayCopy();
                    break;
                default;
                    $array[$rp->name] = self::valueClosure($valueClosure, $val);
                    break;
            }

            // do extra work when $val is an array
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if ($v instanceof $base) {
                        $val[$k] = $v->toArray($valueClosure);
                    } else if (\NObjects\Validate::isAssociativeArray($v)) {
                        $newObj = new \NObjects\Object($v);
                        $val[$k] = $newObj->toArray($valueClosure);
                    } else {
                        $val[$k] = self::valueClosure($valueClosure, $v);
                    }
                }
                $array[$rp->name] = $val;
            }
        }

        return $array;
    }

    /**
     * Determines the accessor method name for $propName. Returns false if no accessor is available.
     *
     * @param string $propName
     *
     * @return string|bool
     */
    protected function _getAccessorMethod($propName)
    {
        $camelProp = ucfirst($propName);

        $funcName = 'get' . $camelProp;
        if (method_exists($this, $funcName)) {
            return $funcName;
        }

        foreach (array('is', 'has') as $prefix) {
            // Cover the cases where the property '{is/has}Something' has accessor '{is/has}Something()'
            if ($prefix == strtolower(substr($camelProp, 0, strlen($prefix)))) {
                $funcName = strtolower(substr($camelProp, 0, 1)) . substr($camelProp, 1);
            } else {
                $funcName = $prefix . $camelProp;
            }

            if (method_exists($this, $funcName)) {
                return $funcName;
            }
        }

        // Fall-through
        return false;
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
     * @param callable $valueClosure
     * @return array
     */
    public function getPropertyValues($valueClosure = null)
    {
        return array_values($this->toArray($valueClosure));
    }

    /**
     * Return object as a JSON string.
     *
     * @param callable $valueClosure
     * @return string
     */
    public function toJSON($valueClosure = null)
    {
        return json_encode($this->toArray($valueClosure));
    }

    // hinting helpers

    /**
     * @param array $value
     * @param $valueType
     * @return array
     */
    public function _typeHintArray(&$value, $valueType)
    {
        foreach ($value as $k => $v) {
            $value[$k] = $this->_typeHint($v, $valueType);
        }
        return $value;
    }

    /**
     * @param string $value
     * @param string $valueType
     * @return mixed
     */
    public function _typeHint($value, $valueType)
    {
        if (is_null($value)) return null;

        if (!$value instanceof $valueType) {
            if (class_exists($valueType)) {
                $value = new $valueType($value ? $value : null);
            } else {
                settype($value, $valueType);
            }
        }
        return $value;
    }

    /**
     * @param $value
     * @return string|null
     */
    public function _initStr($value)
    {
        return $this->_initString($value);
    }

    /**
     * @param $value
     * @return bool|null
     */
    public function _initBool($value)
    {
        return $this->_typeHint($value, 'boolean');
    }

    /**
     * @param $value
     * @return int|null
     */
    public function _initInt($value)
    {
        return $this->_typeHint($value, 'int');
    }

    /**
     * @param $value
     * @return double|null
     */
    public function _initDouble($value)
    {
        return $this->_typeHint($value, 'double');
    }

    /**
     * @param $value
     * @return float|null
     */
    public function _initFloat($value)
    {
        return $this->_typeHint($value, 'float');
    }

    /**
     * @param $value
     * @return array|null
     */
    public function _initArray($value)
    {
        return $this->_typeHint($value, 'array');
    }

    /**
     * @param $value
     * @return string|null
     */
    public function _initString($value)
    {
        return $this->_typeHint($value, 'string');
    }

    /**
     * @param $value
     * @param string $valueType
     * @return null|Object
     */
    public function _initInstanceObject($value, $valueType)
    {
        return $this->_typeHint($value, $valueType);
    }

    /**
     * @param $value
     * @param string $valueType
     * @return null|\ArrayObject
     */
    public function _initInstanceArrayObject($value, $valueType)
    {
        if (is_null($value)) return null;

        if (!$value instanceof \ArrayObject) {
            $value = new \ArrayObject($value);
        }

        $this->_typeHintArray($value, $valueType);

        return $value;
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

    /**
     * @static
     * @param null $valueClosure
     * @return mixed
     */
    public static function valueClosure($valueClosure = null)
    {
        $args = func_get_args();
        $value = $args[1];

        if (is_callable($valueClosure)) {
            unset($args[0]);
            $value = call_user_func_array($valueClosure, $args);
        }

        return $value;
    }

}
