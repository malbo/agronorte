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
     * Check for active cookie
     * Check if account has activity for the last COOKIE_TTL seconds
     * 
     * @return boolean
     */
    private static function cookie()
    {
        // check enviroment
        // always return true in dev enviroment
        if (Configuration::enviroment() === Configuration::MODE_DEV)
        {
            return true;
        }
        
        // cookies settings, only for production enviroment
        $cookie     = filter_input(INPUT_COOKIE, self::COOKIE, FILTER_NULL_ON_FAILURE);
        $validation = false !== $cookie ? $cookie : false;
        if(true === $validation) 
        {
            $value  = $cookie[self::COOKIE];
            setcookie(self::COOKIE, $value, time() + self::TTL, "/");
            return true;
        } 
        else 
        {
            return false;
        }
    }
    
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
        $user = User::load(['`email`' => $email, '`status`' => Categorizations::status(true)['active']]);

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
            if (Categorizations::status(true)['active'] !== (int) $user->status)
            {
                $return['success']  = false;
                $return['message']  = self::$errors['general'];
            }
            else 
            {
                $return['success']  = true;
                $return['message']  = $user;
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
     * @param array $account Account data
     * 
     * @return array
     */
    public static function permissions($account)
    {
        // cookies and sessions
        // cookies disabled for now false === self::checkForActiveCookie() ||
        if (is_null($account['user']['status']) || (int) $account['user']['status'] === Categorizations::status(true)['inactive']) 
        {
            session_unset();
            session_destroy();
            
            return [
                'valid'  => false,
                'url'    => 'index.php',
            ];
        }
        
        // role assigned validation
        if (is_null($account['user']['role']) || true === empty($account['user']['role']))
        {
            return [
                'valid'  => false,
                'url'    => 'error.php?e=403',
            ];
        }
        
        // file access validation
        $file           = basename($_SERVER["SCRIPT_FILENAME"], '.php');
        $valid_access   = true;        
        $url            = isset(parse_url(basename($_SERVER['REQUEST_URI']))['query']) ? parse_url(basename($_SERVER['REQUEST_URI']))['query'] : null;
        parse_str($url);
        switch ($file)
        {
            case 'balance':
                if(true === in_array($account['user']['id'], Configuration::$blocked_users))
                {
                    $valid_access = false;
                }
                break;
                
//            case 'landings':
//                if(false === Exceptions::landings($account['user']['id']))
//                {
//                    $valid_access = false;
//                }
//                break; 
                
            case 'campaign':
                if(false === empty($id))
                {
                    // campaign data ($id came from parse_str function)
                    $campaign   = Campaign::load(['`campaign_id`' => $id]);

                    // get advertiser id
                    $advertiser = User::load(['`advertiser_id`' => $campaign->advertiser_id])->id;

                    // recursive users
                    $params['id_user'] = $account['user']['role'] === Categorizations::roles(true)['admin'] ? $account['user']['id'] : $account['user']['father'];
                    $users      = true === is_null($account['user']['access']) ? Data::recursive($params) : Additional::decode($account['user']['access']);
                    $users_all  = array_flip(array_merge([$advertiser], $users));

                    // compare if user is permitted
                    if(false === isset($users_all[$advertiser]))
                    {
                        $valid_access = false;
                    }
                }
                break;  
  
            case 'certifications':
            case 'creatives_list':
            case 'screenshots':
                if($account['user']['father'] !== Categorizations::status(true)['active'])
                {
                    $valid_access = false;
                }
                break; 
                
            case 'additional':
            case 'data':
            case 'finance':
            case 'profits':
                if($account['user']['father'] !== Categorizations::status(true)['active'] || $account['user']['role'] !== Categorizations::roles(true)['superadmin'])
                {
                    $valid_access = false;
                }
                break;     
  
            case 'invoice':
                if(false === empty($id))
                {
                    // payment data
                    $payment    = Payment::load(['`id`' => $id]);

                    // recursive users
                    $params['id_user'] = $account['user']['role'] === Categorizations::roles(true)['admin'] ? $account['user']['id'] : $account['user']['father'];
                    $users      = true === is_null($account['user']['access']) ? Data::recursive($params) : Additional::decode($account['user']['access']);
                    $users_all  = array_flip(array_merge([$params['id_user']], $users));

                    // compare if user is permitted
                    if(false === isset($users_all[$payment->id_user]))
                    {
                        $valid_access = false;
                    }
                }
                break;
            
            case 'user':
                if(false === empty($id))
                {
                    // recursive users
                    $params['id_user'] = $account['user']['role'] === Categorizations::roles(true)['admin'] ? $account['user']['id'] : $account['user']['father'];
                    $users      = true === is_null($account['user']['access']) ? array_flip(Data::recursive($params)) : array_flip(Additional::decode($account['user']['access']));

                    // compare if user is permitted
                    if(false === isset($users[$id]))
                    {
                        $valid_access = false;
                    }
                }
                break;

            default:
                    $valid_access = true;
                break;
        }
        
        if(false === $valid_access)
        {
            return [
                'valid'  => false,
                'url'    => 'error.php?e=401',
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