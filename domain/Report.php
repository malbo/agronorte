<?php

/**
 * Report.php  Repport class definition for templates.
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
use Agronorte\tools\Additional;

class Report
{
    /**
     * Error messages
     */
    public static $errors = array(
        'captcha'       => 'reCaptcha validation is required.',
        'empty'         => 'Complete all fields.',
        'error'         => 'There was a problem saving user.'
    );
    
    /**
     * Success messages
     */
    public static $success = array(
        'store'         => 'User has been stored successfuly.',
    );
    
    /**
     * Report id.
     *
     * @access public
     * @var int
     */
    public $id;
    
    /**
     * User id.
     *
     * @access public
     * @var int
     */
    public $id_user;

    /**
     * Report name.
     *
     * @access public
     * @var string
     */
    public $name;

    /**
     * Report id.
     *
     * @access public
     * @var string
     */
    public $report;

    /**
     * Report date created.
     *
     * @access public
     * @var string
     */
    public $date;
    
    /**
     * Standard User constructor
     *
     * @access public
     * @param array $data  User data
     */

    public function __construct(array $data) 
    {
      $this->id         = isset($data['id'])        ? (int)     $data['id']         : null;
      $this->id_user    = isset($data['id_user'])   ? (int)     $data['id_user']    : null;
      $this->name       = isset($data['name'])      ? (string)  $data['name']       : null;
      $this->report     = isset($data['report'])    ? (string)  $data['report']     : null;
      $this->date       = isset($data['date'])      ? (string)  $data['date']       : null;
    }

    /**
     * Load reports values
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
SELECT * FROM reports {$condition}
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
        $permissions    = (int)     $params['permissions'];

        // server-side validations for empty fields
        if (empty($name) || empty($lastname) || empty($email) || empty($password))
        {
            $return['success']  = false;
            $return['message']  = self::$errors['empty'];
            
            return json_encode($return);
        }
        
        // define query for profile
        $query = <<<SQL
INSERT INTO users 
    (`id`, `name`, `lastname`, `email`, `password`, `status`, `role`, `permissions`) 
    VALUES 
    (:id, :name, :lastname, :email, :password, :status, :role, :permissions)
    ON DUPLICATE KEY UPDATE `name` = :name2, `lastname` = :lastname2, `email` = :email2, `password` = :password2, `status` = :status2, `role` = :role2, `permissions` = :permissions2
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
        $statement->bindParam(':permissions',   $permissions, \PDO::PARAM_INT);
        $statement->bindParam(':permissions2',  $permissions, \PDO::PARAM_INT);

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