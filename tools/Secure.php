<?php

/**
 * Secure.php  Global secure control.
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
use Agronorte\domain\User;
use Agronorte\tools\Additional;
use Agronorte\tools\Categorizations;

class Secure
{
    /**
     * Constants
     */
    const COOKIE    = 'ra-secure-token';
    const TTL       = 7200;
    
    /**
     * Errors
     */
    public static $errors = array(
        'captcha'   => 'reCaptcha validation is required.',
        'empty'     => 'All fields are required.',
        'invalid'   => 'Type a valid e-mail.',
        'user'      => 'This account does not exists.',
        'inactive'  => 'This account does not belong to this platform.',
        'expired'   => 'Your session has expired after 120 minutes of inactivity. Please Login again.',
        'password'  => 'Invalid password.',
        'general'   => 'E-mail or password are incorrect. Type another one.'
    );
    
    /**
     * Error Codes to page
     */
    public static $errorCodes = array(
        401 => [
            'number'        => '401',
            'title'         => 'No permission',
            'description'   => 'You don\'t have permission to access this page. Please contact system administrator.',
        ],
        403 => [
            'number'        => '403',
            'title'         => 'Forbidden',
            'description'   => 'You don\'t have privileges to access this page. Please contact system administrator.',
        ],
        404 => [
            'number'        => '404',
            'title'         => 'Page not found',
            'description'   => 'Sorry, but the page you are looking for has note been found. Try checking the URL for error, then hit the refresh button on your browser or try found something else in our platform.',
        ],
    );

    /**
     * Login
     * 
     * @param object $params
     * @return boolean
     */
    public static function login($params)
    {
        $email      = (string) $params['email'];
        $password   = (string) $params['password'];
        $return     = [];
        $captcha    = self::reCaptcha($params);
        
        // server-side validations for captcha validation
        if (false === $captcha)
        {
            $return['success']  = false;
            $return['message']  = self::$errors['captcha'];
            
            return json_encode($return);
        }
        
        // server-side validations for empty fields
        if (empty($email) || empty($password))
        {
            $return['success']  = false;
            $return['message']  = self::$errors['empty'];
            
            return json_encode($return);
        }
        
        // server-side validations for email
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $return['success']  = false;
            $return['message']  = self::$errors['invalid'];
            
            return json_encode($return);
        }
        
        // get and validate user
        $user = User::load(['`email`' => $email, '`status`' => Categorizations::status(true)['activo']]);

        // check if user exists
        if (false === $user)
        {
            $return['success']  = false;
            $return['message']  = self::$errors['general'];
        }
        elseif (false === hash_equals($user->password, crypt($password, $user->password)))
        {
            $return['success']  = false;
            $return['message']  = self::$errors['general'];            
        }
        else 
        {
            // check if user is active
            if (Categorizations::status(true)['activo'] !== (int) $user->status)
            {
                $return['success']  = false;
                $return['message']  = self::$errors['general'];
            }
            else 
            {
                $return['success']  = true;
                $return['location'] = 'dashboard.php';

                // session handler
                // set session.gc_maxlifetime = 7200 in php.ini
                session_save_path();
                session_start();
                $_SESSION['session'] = (array) $user;
            }
        }

        return json_encode(array_filter($return));
    }

    /**
     * Security validations for permissions platform
     * @param array $user User data
     * 
     * @return array
     */
    public static function permissions($user)
    {
        // cookies and sessions
        // cookies disabled for now false === self::checkForActiveCookie() ||
        if (is_null($user->status) || (int) $user->status === Categorizations::status(true)['inactivo']) 
        {
            session_unset();
            session_destroy();
            
            return [
                'valid'  => false,
                'url'    => 'index.php',
            ];
        }
        
        // role assigned validation
        if (is_null($user->role) || true === empty($user->role))
        {
            return [
                'valid'  => false,
                'url'    => 'error.php',
            ];
        }
        
        // file access validation
        $file           = basename($_SERVER["SCRIPT_FILENAME"], '.php');
        $valid_access   = true;        
        $url            = isset(parse_url(basename($_SERVER['REQUEST_URI']))['query']) ? parse_url(basename($_SERVER['REQUEST_URI']))['query'] : null;
        parse_str($url);
        switch ($file)
        {
            default:
                break;
        }
        
        if(false === $valid_access)
        {
            return [
                'valid'  => false,
                'url'    => 'error.php',
            ];            
        }

        return [
            'valid'  => true,
            'url'    => null,
        ];
    }
    
    /**
     * reCaptcha validation
     * 
     * @param object $params
     * @return boolean
     */
    public static function reCaptcha($params)
    {
        // validate captcha
        $data = array(
            'secret'    => '6LcrUjsjAAAAANswpB9JZw4XKheFurh5RNzk_Qhc',
            'response'  => $params['security'],
            'remoteip'  => Configuration::server('REMOTE_ADDR'),
        );

        $requestURL = 'https://www.google.com/recaptcha/api/siteverify?';

        $ch = curl_init() or die("Fail cURL data init: ". curl_error()); 
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $requestURL,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => http_build_query($data)
        ));
        $response = curl_exec($ch) or die("Fail cURL data exec: ". curl_error($ch));
        curl_close($ch);  
        $return = json_decode($response, true);

        // captcha validation
        if(true === $return['success'])
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}