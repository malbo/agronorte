<?php

/**
 * Simple autoloader according standar PSR-0
 * 
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package tools.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

function classAutoLoader($className)
{
    $prefix = 'Agronorte\\';
    $home   = realpath(dirname(__FILE__) . '/../');
    $path   = str_replace("\\", DIRECTORY_SEPARATOR, str_replace($prefix, "", $className));

    require_once($home . DIRECTORY_SEPARATOR . $path . '.php');
}

spl_autoload_register('classAutoLoader');