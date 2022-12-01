<?php

/**
 * Connector.php  Connector Ajax.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package inc.frontend.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\frontend\inc;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\tools\Additional;

class Connector
{
    /**
     * Classes definitions
     */
    const CATEGORIZATIONS   = 'Categorizations';
    const REPORT            = 'Report';
    const SECURE            = 'Secure';
    const USER              = 'User';
    
    /**
     * Get connection between JS and PHP
     * 
     * @params object $token
     * @return mixed
     */
    public static function init($token)
    {
        if (false === $token || empty($token))
        {
            return false;
        }
        else
        {
            switch ($token['class'])
            {
                // Core
                // case self::CONFIGURATION:
                //     $return = call_user_func("\\Agronorte\\core\\{$token['class']}::{$token['method']}", $token);
                //     break;
                
                // Cron
                // case self::PLATFORM:
                //     $return = call_user_func("\\Agronorte\\cron\\dsp\\{$token['class']}::{$token['method']}", $token);
                //     break;
                
                // Domain
                case self::REPORT:
                case self::USER:
                    $return = call_user_func("\\Agronorte\\domain\\{$token['class']}::{$token['method']}", $token);
                    break;
                
                // Tools
                case self::CATEGORIZATIONS:
                case self::SECURE:
                    $return = call_user_func("\\Agronorte\\tools\\{$token['class']}::{$token['method']}", $token);
                    break;
                
                default:
                    break;
            }

            // echoing JSON response from PHP to JS
            echo $return ? : false;
        }
    }
}

$token = null !== filter_input(INPUT_POST, 'params', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY) ? filter_input(INPUT_POST, 'params', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY) : false;
Connector::init($token);