<?php

/**
 * SqlDb.php  Connector for MySQL.
 *
 * Copyright (C) 2022 Foodtech <alboresmariano@gmail.com>
 *
 * @package core.Foodtech
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Foodtech\core;

use Foodtech\core\RedisDb;

class SqlDb
{
    /* Constants */
    const DBHOST        = 'DBHOST';
    const DBPASSWORD    = 'DBPASSWORD';
    const DBUSERNAME    = 'DBUSERNAMEFOODTECH';
    const PFDBNAME      = 'PFDBNAME';
    
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
            // get PDO from ini files
            $client     = 'cache-console';
            $host       = RedisDb::getClient($client)->get(self::DBHOST);
            $dbname     = RedisDb::getClient($client)->get(self::PFDBNAME);
            $username   = RedisDb::getClient($client)->get(self::DBUSERNAME);
            $password   = RedisDb::getClient($client)->get(self::DBPASSWORD);
            
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