<?php

namespace NObjects;

/**
 * Legacy class that has a PHP 7 incompatible name.
 *
 * @deprecated
 */
class Object extends Nobject
{
    /**
     * Public constructor
     * If an associative array is passed as an argument, hydrate object using calls fromArray().
     */
    public function __construct()
    {
        @trigger_error('Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.', E_USER_DEPRECATED);

        // Cope with variable arguments. func_get_args is context-sensitive and can't be inlined
        // (yay interpreter side-effects)
        $args = func_get_args();
        call_user_func_array('parent::__construct', $args);
    }

    // static methods

    /**
     * Get an array of all class parents.
     *
     * @link http://us.php.net/manual/en/function.get-parent-class.php#57548
     * @param string $class
     * @param bool $reverseOrder
     * @return array
     *
     * @deprecated
     */
    public static function ancestors($class, $reverseOrder = false)
    {
        @trigger_error('Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.', E_USER_DEPRECATED);

        return parent::ancestors($class, $reverseOrder);
    }

    /**
     * @static
     * @param null $valueClosure
     * @return mixed
     *
     * @deprecated
     */
    public static function valueClosure($valueClosure = null)
    {
        @trigger_error('Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.', E_USER_DEPRECATED);

        $args = func_get_args();

        return call_user_func_array('parent::valueClosure', $args);
    }
}
