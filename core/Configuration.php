<?php

/**
 * Configuration.php  Global Configuration set-up.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package core.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\core;

use Agronorte\tools\Additional;

class Configuration
{
    
    /**
     * Configuration for templates
     */
    const FANTASY                       = 'Agronorte';
    const COMPANY                       = 'Agronorte';
    const ADDRESS1                      = '1441 Brickell Avenue #1018';
    const ADDRESS2                      = 'Miami, FL, 33131, United States';
    const PHONE                         = '+1 (786) 753-7839';
    const ADMIN_MAIL                    = 'info@planckfood.com';
    const NO_REPLY_MAIL                 = 'no-reply@planckfood.com';
    
    /**
     * Constants
     * Don't change anything from here because unpredictable actions could occur
     */
    const MODE_DEV                      = 'dev';
    const MODE_PROD                     = 'production';
    const KEY_SEPARATOR                 = ':';
    const JOKER                         = '*';
    const MONEY                         = '$';
    const PERCENT                       = '%';
    const COMMA                         = ',';
    const QUESTION                      = '?';
    const THOUSAND                      = '.';
    const UNDEFINED                     = 'Undefined';
    
    const TTL_INF                       = 0;
    const TTL_MINUTE                    = 60;
    const TTL_QUARTER                   = 900;
    const TTL_HALF_HOUR                 = 1800;
    const TTL_HOUR                      = 3600;
    const TTL_HALF_DAY                  = 43200;
    const TTL_DAY                       = 86400;
    const TTL_WEEK                      = 604800;
    const TTL_MONTH                     = 2592000;
    
    /**
     * Get Server and execution environment information
     * 
     * @param string $var
     * @return string
     */
    public static function server($var)
    {
        return filter_input(INPUT_SERVER, $var);
    }
    
    /**
     * Get Server and execution environment information
     * 
     * @param string $var
     * @return string
     */
    public static function enviroment()
    {
        // get enviroment from configuration file
        $config = parse_ini_file(realpath(dirname(__FILE__) . '/config.conf'), true);
        return $config['enviroment']['mode'];
    }
    
    /**
     * Get Server and execution environment information
     * 
     * @param string $var
     * @return string
     */
    public static function secure()
    {
        if((self::server('HTTP_X_FORWARDED_PROTO') === 'http'))
        {
            Header("HTTP/1.1 301 Moved Permanently");
            Header("Location: https://" . self::server('HTTP_HOST') . self::server('REQUEST_URI'));
        }
    }
}

// set timezone
date_default_timezone_set('UTC');