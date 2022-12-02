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
        'empty'         => 'Complete all fields.',
        'error'         => 'Hubo un problema guaradndo el reporte.'
    );
    
    /**
     * Success messages
     */
    public static $success = array(
        'store'         => 'El reporte ha sido guardado.',
        'delete'        => 'El reporte ha sido borrado.',
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
     * Delete report values
     * 
     * @param array $params
     * @return boolean
     */
    public static function delete(array $params)
    {
        $id = (int) $params['id'];

        // define query for profile
        $query = <<<SQL
DELETE FROM reports WHERE `id` = :id
SQL;
        
        // pepare statement
        $statement = SqlDb::getPdo()->prepare($query);
        
        // bind params
        $statement->bindParam(':id', $id, \PDO::PARAM_INT);

        // get results
        if($statement->execute())
        {
            $return['success'] = true;    
            $return['message'] = self::$success['delete'];  
        } 
        else
        {
            $return['success'] = false;    
            $return['message'] = self::$errors['error'];  
        }

        return json_encode($return);
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
     * Store report values
     * 
     * @param array $params
     * @return boolean
     */
    public static function store(array $params)
    {
        $id         = true === isset($params['id']) && !empty($params['id']) ? (int) $params['id'] : null;
        $id_user    = (int)     $params['id_user'];
        $name       = (string)  $params['name'];
        $report     = (string)  $params['report'];

        // define query for profile
        $query = <<<SQL
INSERT INTO reports 
    (`id`, `id_user`, `name`, `report`) 
    VALUES 
    (:id, :id_user, :name, :report)
    ON DUPLICATE KEY UPDATE `name` = :name2, `report` = :report2
SQL;
        
        // pepare statement
        $statement = SqlDb::getPdo()->prepare($query);
        
        // bind params
        $statement->bindParam(':id',        $id, \PDO::PARAM_INT);
        $statement->bindParam(':id_user',   $id_user, \PDO::PARAM_INT);
        $statement->bindParam(':name',      $name, \PDO::PARAM_STR);
        $statement->bindParam(':name2',     $name, \PDO::PARAM_STR);
        $statement->bindParam(':report',    $report, \PDO::PARAM_STR);
        $statement->bindParam(':report2',   $report, \PDO::PARAM_STR);

        // execute
        if($statement->execute())
        {
            $lastId = is_null($id) ? SqlDb::getPdo()->lastInsertId() : $params['id'];
            $return['id'] = $lastId;  
        }

        return json_encode($return);
    }
}