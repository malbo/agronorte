<?php

/**
 * Scaling.php  Scale utilities.
 *
 * Copyright (C) 2022 Foodtech <alboresmariano@gmail.com>
 *
 * @package tools.Foodtech
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Foodtech\tools;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/Autoload.php'));

abstract class Scaling
{
    /**
     * Scaling factor
     *
     * @var int
     */
    protected static $scale = 1000;

    /**
     * Convert a given value to the class' inverse scale
     *
     * @param int|float $val  value to convert
     * @return int
     */
    public static function divide($val = 0) 
    {
        return $val / static::$scale;
    }

    /**
     * Convert a given value to the class' inverse scale
     *
     * @param int|float $val  value to convert
     * @return int
     */
    public static function multiply($val = 0) 
    {
        return $val * static::$scale;
    }
}