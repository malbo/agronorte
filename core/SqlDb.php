<?php

/**
 * SqlDb.php  Connector for MySQL.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package core.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\core;

use Agronorte\tools\Additional;
use Agronorte\tools\Dotenv;

class SqlDb
{
    /**
     * Return a PDO object for the given environment
     *
     * @access public
     * @static
     * @return null|\PDO
     * @throws Exception
     */
    public static function getPdo() 
    {
        $pdo = null;

        // if this is the first time we get called
        if (null === $pdo) 
        {
            // get PDO from .env files
            Dotenv::load(realpath(dirname(__FILE__) . '/../.env'));
            $host       = getenv('DBHOST');
            $dbname     = getenv('DBDB');
            $username   = getenv('DBUSER');
            $password   = getenv('DBPASSWORD');
            
            // Set DSN
            $dsn    = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';
            
            // Set options
            $options = array(
                \PDO::ATTR_PERSISTENT               => true,
                \PDO::ATTR_ERRMODE                  => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                \PDO::ATTR_EMULATE_PREPARES         => false
            );
      
            $pdo    = new \PDO($dsn, $username, $password, $options);
        }

        // return cached PDO
        return $pdo;
    }
}