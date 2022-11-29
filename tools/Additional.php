<?php

/**
 * Additional.php  General utilities.
 * Most of them are complementary from a JS function.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package tools.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\tools;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/Autoload.php'));

use Agronorte\core\Configuration;

class Additional
{
    /**
     * SQL conditions format
     * 
     * @param array $conditions
     * @return string
     */
    public static function conditions($conditions)
    {
        if (!empty($conditions))
        {
            $values = 'WHERE ';
            foreach ($conditions as $key => $value)
            { 
                $values.= "{$key} = '{$value}' AND ";
            }
            
            // remove last "AND"
            $condition = substr($values, 0, -4);
        }
        else 
        {
            $condition = '';
        }
 
        return $condition;
    }
    
    /**
     * Generate log line
     * Suggested call: Additional::log("$msg (" . __FILE__ . "|" . __FUNCTION__ . "|" . __LINE__ . ")", $value);
     * 
     * @param string $msg
     * @param mixed $value
     * @return boolean
     */
    public static function log($msg, $value)
    {        
        return error_log("{$msg} : " . var_export($value, true));
    }

    /**
     * Random string
     * 
     * @param int $len
     * @return string
     */
    public static function randomString($len)
    {
        $result = '';
        $chars = 'ABCDEFGHIGKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$_?!-0123456789';
        $charArray = str_split($chars);
        for($i = 0; $i < $len; $i++)
        {
                $randItem = array_rand($charArray);
                $result .= "".$charArray[$randItem];
        }
        return $result;
    }
}