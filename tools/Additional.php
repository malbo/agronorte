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
use Agronorte\tools\Scaling;

class Additional
{
    /**
     * Check for empty values
     * 
     * @param string $value
     * @return mixed
     */
    public static function checkValue($value)
    {
        return !is_null($value) ? $value : null;
    }
    
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
     * Format number values
     * 
     * @param string $value
     * @param string $dec
     * @return string
     */
    public static function number($value, $dec=2)
    {
        return number_format($value, $dec, Configuration::COMMA, Configuration::THOUSAND);
    }
    
    /**
     * Format money values
     * 
     * @param string $value
     * @param string $dec
     * @return string
     */
    public static function money($value, $dec=2)
    {
        return Configuration::MONEY . number_format(Scaling::divide($value), $dec, Configuration::COMMA, Configuration::THOUSAND);
    }
    
    /**
     * Format percentage values
     * 
     * @param string $value
     * @param string $dec
     * @return string
     */
    public static function percent($value, $dec=2)
    {
        return Configuration::PERCENT . number_format($value, $dec, Configuration::COMMA, Configuration::THOUSAND);
    }
    
    /**
     * Normalize money values to store into MySQL
     * 
     * @param string $money
     * @return int
     */
    public static function normalize($money)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

        return (float) str_replace(',', '.', $removedThousendSeparator);
    }
    
    /**
     * Ratio between two values
     * 
     * @param int $value1
     * @param int $value2
     * @return int
     */
    public static function ratio($value1, $value2)
    {
        return $value2 > 0 ? $value1 / $value2 * 100 : 0;
    }
    
    /**
     * Change between two values
     * 
     * @param int $value1
     * @param int $value2
     * @return int
     */
    public static function change($value1, $value2)
    {
        return $value1 > 0 ? (($value2 - $value1) / $value1) * 100 : 0;
    }
    
    /**
     * Nice number
     * 
     * @param int $value
     * @return string
     */
    function bdNiceNumber($value, $dec=0, $sign='') 
    {
        // first strip any formatting
        $n = intval($value);

        // is this a number
        if(false === is_numeric($n))
        {
            return false;
        }
        
        // is this a NO VALUE
        if(empty($n))
        {
            return  $sign . Additional::number($n, $dec);
        }

        // now filter it
        switch ($n)
        {
            case $n > 1000000000000:
                $return = $sign . round(($n / 1000000000000), 1) . 'T';
                break;
                
            case $n > 1000000000:
                $return = $sign . round(($n / 1000000000), 1) . 'B';
                break;
                
            case $n > 1000000:
                $return = $sign . round(($n / 1000000), 1) . 'M';
                break;
                
            case $n > 1000:
                $return = $sign . round(($n / 1000), 1) . 'K';
                break;

            default:
                $return = $sign . Additional::number($n, $dec);
                break;
        }

        return $return;
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
     * Generate txt output
     * 
     * @param string $destination
     * @param string $content
     * @return boolean
     */
    public static function txt($destination, $content)
    {  
        $fp = fopen($destination, "wb");
        fwrite($fp, $content);

        return fclose($fp);
    }
    
    /**
     * Generate month of the year

     * @return array
     */
    public static function months()
    {
        $return = [];
        for ($i=1; $i<=12; $i++)
        {
            $value = date('m', mktime(0, 0, 0, $i, 10));
            $return[$value] = $value;
        }
        
        return $return;
    }
    
    /**
     * Generate years

     * @return array
     */
    public static function years()
    {
        $return = [];
        $init   = date('Y');
        $end    = $init + 25;
        for ($i=$init; $i<=$end; $i++)
        {
            $return[$i] = $i;
        }
        
        return $return;
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
    
    /**
     * Word wrapping
     * 
     * @param string $value Word
     * @param int $lenght Max word lenght
     * @return string
     */
    public static function wordWrapping($value, $lenght)
    {
        return wordwrap($value, $lenght);
    }
    
    /**
     * Word cut
     * 
     * @param string $value Word
     * @param int $lenght Max word lenght
     * @return string
     */
    public static function wordCut($value, $lenght)
    {
        $strlen = strlen($value);
        return $strlen < $lenght ? $value : substr($value, 0, $lenght) . '...';
    }
}