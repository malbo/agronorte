<?php

/**
 * User.php  User class definition for templates.
 *
 * Copyright (C) 2022 Agronorte <alboresmariano@gmail.com>
 *
 * @package domain.Agronorte
 * @author  Mariano Alborés <alboresmariano@gmail.com>
 */

namespace Agronorte\domain;

// simple autoloader according standard PSR-0
require_once(realpath(dirname(__FILE__) . '/../tools/Autoload.php'));

use Agronorte\core\Configuration;
use Agronorte\core\SqlDb;
use Agronorte\tools\Categorizations;
use Agronorte\tools\Additional;
use Agronorte\tools\Secure;

class User
{
    /**
     * Error messages
     */
    public static $errors = array(
        'captcha'       => 'reCaptcha validation is required.',
        'empty'         => 'Complete all fields.',
        'error'         => 'There was a problem saving user.',
        'exist'         => 'E-mail or password are incorrect. Type another one.',
        'invalid'       => 'Type a valid e-mail.',
        'password'      => 'There was a problem updating password.',
        'related'       => 'You can not relate to this account.'
    );
    
    /**
     * Success messages
     */
    public static $success = array(
        'delete'        => 'User has been deleted.',
        'password'      => 'Password has been stored.',
        'store'         => 'User has been stored successfuly.',
    );
    
    /**
     * User id.
     *
     * @access public
     * @var int
     */
    public $id;

    /**
     * User name.
     *
     * @access public
     * @var string
     */
    public $name;

    /**
     * User lastname.
     *
     * @access public
     * @var string
     */
    public $lastname;

    /**
     * User e-mail.
     *
     * @access public
     * @var string
     */
    public $email;

    /**
     * User password.
     *
     * @access public
     * @var string
     */
    public $password;

    /**
     * User status.
     *
     * @access public
     * @var string
     */
    public $status;

    /**
     * User role.
     *
     * @access public
     * @var int
     */
    public $role;

    /**
     * User related to.
     *
     * @access public
     * @var int
     */
    public $related;

    /**
     * User permissions (readonly or read/write).
     *
     * @access public
     * @var int
     */
    public $permissions;

    /**
     * User date created.
     *
     * @access public
     * @var string
     */
    public $created;
    
    /**
     * Standard User constructor
     *
     * @access public
     * @param array $data  User data
     */

    public function __construct(array $data) 
    {
      $this->id             = isset($data['id'])            ? (int)     $data['id']             : null;
      $this->name           = isset($data['name'])          ? (string)  $data['name']           : null;
      $this->lastname       = isset($data['lastname'])      ? (string)  $data['lastname']       : null;
      $this->email          = isset($data['email'])         ? (string)  $data['email']          : null;
      $this->password       = isset($data['password'])      ? (string)  $data['password']       : null;
      $this->status         = isset($data['status'])        ? (int)     $data['status']         : null;
      $this->role           = isset($data['role'])          ? (int)     $data['role']           : null;
      $this->related        = isset($data['related'])       ? (int)     $data['related']        : null;
      $this->permissions    = isset($data['permissions'])   ? (int)     $data['permissions']    : null;
      $this->created        = isset($data['created'])       ? (string)  $data['created']        : null;
    }

    /**
     * Check if it is admin user
     * 
     * @param int $id
     * @return boolean
     */
    public static function admin($id)
    {
        return Configuration::ADMIN_USER_ID === (int) $id ? true : false;
    }

    /**
     * Delete users
     * It is only a logical deletion, values persists for further usage
     * 
     * @param array $params
     * @return boolean
     */
    public static function delete(array $params)
    {
        $user   = self::load(['`id`' => (int) $params['id']]);
        $email  = $user->email . '**DELETED**';
        
        $query = <<<SQL
UPDATE users SET `status` = :status, `email` = :email WHERE `id` = :id
SQL;

        // pepare statement
        $statement = SqlDb::getPdo()->prepare($query);
        
        // bind params
        $statement->bindParam(':id',        $params['id'], \PDO::PARAM_INT);
        $statement->bindParam(':email',     $email, \PDO::PARAM_STR);
        $statement->bindParam(':status',    Categorizations::status(true)['deleted'], \PDO::PARAM_INT);

        // get results
        $statement->execute();
        
        $return['success'] = true;    
        $return['message'] = self::$success['delete'];    
        
        return json_encode($return);
    }
    
    /**
     * Check if account exists
     * 
     * @param array $params
     * @return boolean
     */
    public static function exists(array $params)
    {
        if ($params['oldemail'] !== $params['email'])
        {
            // define query       
            $query = <<<SQL
SELECT * FROM users WHERE `email` = :email AND `status` != :status
SQL;
            // pepare statement
            $statement = SqlDb::getPdo()->prepare($query);

            // bind params
            $statement->bindParam(':email',     $params['email'], \PDO::PARAM_STR);
            $statement->bindParam(':status',    Categorizations::status(true)['pending'], \PDO::PARAM_INT);

            // get results
            $statement->execute();

            return false === $statement->fetch(\PDO::FETCH_ASSOC) ? true : false;  
        }
        
        return true;
    }
    
    /**
     * Load users values
     * 
     * @param array $conditions SQL conditions to looking for
     * @param boolean $multiple Single or multiple selection
     * @return object/array
     */
    public static function load(array $conditions, $multiple=false)
    {
        $condition = Additional::conditions($conditions);

        // define query       
        $query = <<<SQL
SELECT * FROM users {$condition}
SQL;
        // pepare statement
        $statement = SqlDb::getPdo()->prepare($query);

        // get results
        $statement->execute();  
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        // format for empty responses
        if (true === empty($result))
        {
            return false;
        }
        
        // get related data
        if (true === $multiple)
        {
            return $result;
        }
        else
        {
            return new self($result[0]);
        }      
    }
    
    /**
     * Password hash
     * Comparission between passwords came from hashed password
     * 
     * @param string $new_password
     * @param string $old_password
     * @return string
     */
    public static function password($new_password, $old_password)
    {
        // A higher "cost" is more secure but consumes more processing power
        $cost = 10;

        // Create a random salt
//        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        $salt = strtr(base64_encode(random_bytes(16)), '+', '.');

        // Prefix information about the hash so PHP knows how to verify it later.
        // "$2a$" Means we're using the Blowfish algorithm. The following two digits are the cost parameter.
        $salt = sprintf("$2a$%02d$", $cost) . $salt;

        // Hash the password with the salt
        $hash = crypt($new_password, $salt);
        
        return $new_password === $old_password ? $old_password : $hash;
    }
    
    /**
     * Reset user password
     * 
     * @param array $params
     * @return boolean
     */
    public static function reset($params)
    {
        // define query       
        $query = <<<SQL
UPDATE users SET password = :password WHERE `email` = :email AND `token` = :token
SQL;
        // pepare statement
        $statement = SqlDb::getPdo()->prepare($query);

        // bind params
        $statement->bindParam(':email',     $params['email'], \PDO::PARAM_STR);
        $statement->bindParam(':token',     $params['token'], \PDO::PARAM_STR);
        $statement->bindParam(':password',  self::password($params['password'], ''), \PDO::PARAM_STR);

        // execute
        if (true === $statement->execute())
        {
            $return['success'] = true; 
            $return['message'] = self::$success['password'];
        }
        else
        {
            $return['success'] = false; 
            $return['message'] = self::$errors['password'];
        }

        return json_encode($return);
    }

    /**
     * Store users values
     * 
     * @param array $params
     * @return boolean
     */
    public static function store(array $params)
    {
        $id             = true === isset($params['id']) && !empty($params['id']) ? (int) $params['id'] : null;
        $name           = (string)  $params['name'];
        $lastname       = (string)  $params['lastname'];
        $email          = (string)  trim($params['email']);
        $password       = (string)  $params['password'];
        $status         = (int)     $params['status'];
        $role           = (int)     $params['role'];
        $related        = true === self::admin($params['id']) || 'null' === $params['id'] ? NULL : (int) $params['related'];
        $father         = true === is_null($params['father']) ? Configuration::ADMIN_USER_ID : (int) $params['father'];
        $access         = true === empty($params['access']) || 'null' === $params['access'] ? NULL : (string) $params['access'];
        $permissions    = (int)     $params['permissions'];

        // server-side validations for empty fields
        if (empty($name) || empty($lastname) || empty($email) || empty($password))
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
        
        // check if account exists
        if (false === self::exists($params))
        {
            $return['success']  = false;
            $return['message']  = self::$errors['exist'];
            
            return json_encode($return);            
        }
        
        // define query for profile
        $query = <<<SQL
INSERT INTO users 
    (`id`, `name`, `lastname`, `email`, `password`, `status`, `role`, `related`, `father`, `permissions`, `access`) 
    VALUES 
    (:id, :name, :lastname, :email, :password, :status, :role, :related, :father, :permissions, :access)
    ON DUPLICATE KEY UPDATE `name` = :name2, `lastname` = :lastname2, `email` = :email2, `password` = :password2, `status` = :status2, `role` = :role2, `related` = :related2, `permissions` = :permissions2, `access` = :access2
SQL;
        
        // pepare statement
        $statement = SqlDb::getPdo()->prepare($query);
        
        // bind params
        // for new users password and old password are the same
        $store_password = self::password($password, $params['oldpassword']);
        
        $statement->bindParam(':id',            $id, \PDO::PARAM_INT);
        $statement->bindParam(':name',          $name, \PDO::PARAM_STR);
        $statement->bindParam(':name2',         $name, \PDO::PARAM_STR);
        $statement->bindParam(':lastname',      $lastname, \PDO::PARAM_STR);
        $statement->bindParam(':lastname2',     $lastname, \PDO::PARAM_STR);
        $statement->bindParam(':email',         $email, \PDO::PARAM_STR);
        $statement->bindParam(':email2',        $email, \PDO::PARAM_STR);
        $statement->bindParam(':password',      $store_password, \PDO::PARAM_STR);
        $statement->bindParam(':password2',     $store_password, \PDO::PARAM_STR);
        $statement->bindParam(':status',        $status, \PDO::PARAM_INT);
        $statement->bindParam(':status2',       $status, \PDO::PARAM_INT);
        $statement->bindParam(':role',          $role, \PDO::PARAM_INT);
        $statement->bindParam(':role2',         $role, \PDO::PARAM_INT);
        $statement->bindParam(':related',       $related, \PDO::PARAM_INT);
        $statement->bindParam(':related2',      $related, \PDO::PARAM_INT);
        $statement->bindParam(':father',        $father, \PDO::PARAM_INT);
        $statement->bindParam(':permissions',   $permissions, \PDO::PARAM_INT);
        $statement->bindParam(':permissions2',  $permissions, \PDO::PARAM_INT);
        $statement->bindParam(':access',        $access, \PDO::PARAM_STR);
        $statement->bindParam(':access2',       $access, \PDO::PARAM_STR);

        // get results
        if($statement->execute())
        {
            $lastId = is_null($id) ? SqlDb::getPdo()->lastInsertId() : $params['id'];
            $return['success'] = true;    
            $return['message'] = self::$success['store'];  
            $return['id']      = $lastId;  
        } 
        else
        {
            $return['success'] = false;    
            $return['message'] = self::$errors['error'];  
        }
        
        
        return json_encode($return);
    }
}