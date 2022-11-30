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
     * Delete folder
     * 
     * @param string $dir Path to directory
     * @return boolean
     */
    public static function deleteFolder($dir) 
    {
        if(false === is_array($dir) && is_dir($dir))
        {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) 
            {
              (is_dir($dir . '/' . $file)) ? self::deleteFolder($dir . '/' . $file) : unlink($dir . '/' . $file);
            }

            return rmdir($dir);
        }
        else
        {
            return true;
        }
    }

    /**
     * Check files data
     * 
     * @param string $path
     * @return array
     */
    public static function filesData($path)
    {
        // prevent empty responses
        if(false === is_dir($path))
        {
            return false;
        }
        
        $files  = array_values(array_diff(scandir($path), ['..', '.']));
        $return = [];
        foreach ($files as  $file)
        {
            $new_file       = str_replace(['+', '%', '$', '?', '¿', '!', '&', '|', ' ', '\'', '/'], "", $file);
            rename("{$path}/{$file}", "{$path}/{$new_file}");
            $file_path      = "{$path}/{$new_file}";
            $extension      = pathinfo($file_path)['extension'];           
            $img_property   = getimagesize($file_path);
            $img_size       = $img_property[0] . 'x' . $img_property[1];
            
            $return[] = [
                'file_extension'    => $extension,
                'file_size'         => $img_size,
                'file_name'         => $new_file,
                'file_path'         => $path . '/' . $new_file  
            ];
        }

        return $return[max(array_keys($return))];
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