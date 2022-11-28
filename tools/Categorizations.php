<?php

/**
 * Categorizations.php  Categorizations.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package tools.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\tools;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\core\SqlDb;
use Agronorte\domain\User;

class Categorizations
{
    /**
     * Constants
     */

    /**
     * Categorizations permissions
     * 
     * @param boolean $flip Permissions all keys with their associated values in an array
     * @return mixed
     */
    public static function permissions($flip=false)
    {
        // define array       
        $return = [
            0   => 'Read Only privileges',
            1   => 'Read/Write privileges',
        ];
        
        return true === $flip ? array_flip($return) : $return;
    }

    /**
     * Categorizations roles
     * 
     * @param boolean $flip Exchanges all keys with their associated values in an array
     * @return mixed
     */
    public static function roles($flip=false)
    {
        // define array       
        $return = [
            1   => 'admin',
            2   => 'user',
            3   => 'superadmin',
        ];
        
        return true === $flip ? array_flip($return) : $return;
    }
    
    /**
     * Categorizations status modes
     * 
     * @param boolean $flip Exchanges all keys with their associated values in an array
     * @return mixed
     */
    public static function status($flip=false)
    {
        // define array       
        $return = [
            0   => 'inactive',
            1   => 'active',
            2   => 'pending',
            3   => 'deleted'
        ];
        
        return true === $flip ? array_flip($return) : $return;
    }
}