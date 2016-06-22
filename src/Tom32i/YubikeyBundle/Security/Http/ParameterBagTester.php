<?php

namespace Tom32i\YubikeyBundle\Security\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;

/**
 * Parameter bag tester
 *
 * Based on Symfony\Component\Security\Http\ParameterBagUtils
 */
final class ParameterBagTester
{
    private static $propertyAccessor;

    /**
     * Test if request has a "parameter" value.
     *
     * Paths like foo[bar] will be evaluated to find deeper items in nested data structures.
     *
     * @param ParameterBag $parameters The parameter bag
     * @param string       $path       The key
     *
     * @return mixed
     *
     * @throws InvalidArgumentException when the given path is malformed
     */
    public static function hasParameterBagValue(ParameterBag $parameters, $path)
    {
        if (false === $pos = strpos($path, '[')) {
            return $parameters->has($path);
        }

        $root = substr($path, 0, $pos);

        if (!$parameters->has($root)) {
            return false;
        }

        if (null === $value = $parameters->get($root)) {
            return false;
        }

        if (null === self::$propertyAccessor) {
            self::$propertyAccessor = new PropertyAccessor(false, true);
        }

        try {
            self::$propertyAccessor->getValue($value, substr($path, $pos));
        } catch (NoSuchIndexException $e) {
            return false;
        }

        return true;
    }

    /**
     * Test if request has request "parameter" value.
     *
     * Paths like foo[bar] will be evaluated to find deeper items in nested data structures.
     *
     * @param Request $request The request
     * @param string  $path    The key
     *
     * @return mixed
     *
     * @throws InvalidArgumentException when the given path is malformed
     */
    public static function hasRequestParameterValue(Request $request, $path)
    {
        if (false === $pos = strpos($path, '[')) {
            return $request->attributes->has($path)
                || $request->query->has($path)
                || $request->request->has($path);
        }

        $root = substr($path, 0, $pos);

        if (!$parameters->has($root)) {
            return false;
        }

        if (null === $value = $request->get($root)) {
            return false;
        }

        if (null === self::$propertyAccessor) {
            self::$propertyAccessor = new PropertyAccessor(false, true);
        }

        try {
            self::$propertyAccessor->getValue($value, substr($path, $pos));
        } catch (NoSuchIndexException $e) {
            return false;
        }

        return true;
    }
}
